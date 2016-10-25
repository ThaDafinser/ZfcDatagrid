<?php
namespace ZfcDatagridTest\DataSource\Doctrine2;

use ZfcDatagridTest\DataSource\DataSourceTestCase;

/**
 *
 * @copyright goes to: https://github.com/doctrine/doctrine2/blob/master/tests/Doctrine/Tests/OrmTestCase.php
 *
 *  @group DataSource
 *  @covers \ZfcDatagrid\DataSource\Doctrine2
 */
abstract class AbstractDoctrine2Test extends DataSourceTestCase
{
    /**
     * The metadata cache that is shared between all ORM tests (except functional tests).
     *
     * @var \Doctrine\Common\Cache\Cache null
     */
    private static $_metadataCacheImpl = null;

    /**
     * The query cache that is shared between all ORM tests (except functional tests).
     *
     * @var \Doctrine\Common\Cache\Cache null
     */
    private static $_queryCacheImpl = null;

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     *
     * @param array $paths
     * @param mixed $alias
     *
     * @return \Doctrine\ORM\Mapping\Driver\AnnotationDriver
     */
    protected function createAnnotationDriver($paths = [], $alias = null)
    {
        if (version_compare(\Doctrine\Common\Version::VERSION, '3.0.0', '>=')) {
            $reader = new \Doctrine\Common\Annotations\CachedReader(new \Doctrine\Common\Annotations\AnnotationReader(), new ArrayCache());
        } elseif (version_compare(\Doctrine\Common\Version::VERSION, '2.2.0-DEV', '>=')) {
            // Register the ORM Annotations in the AnnotationRegistry
                $reader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
            $reader->addNamespace('Doctrine\ORM\Mapping');
            $reader = new \Doctrine\Common\Annotations\CachedReader($reader, new ArrayCache());
        } elseif (version_compare(\Doctrine\Common\Version::VERSION, '2.1.0-BETA3-DEV', '>=')) {
            $reader = new \Doctrine\Common\Annotations\AnnotationReader();
            $reader->setIgnoreNotImportedAnnotations(true);
            $reader->setEnableParsePhpImports(false);
            if ($alias) {
                $reader->setAnnotationNamespaceAlias('Doctrine\ORM\Mapping\\', $alias);
            } else {
                $reader->setDefaultAnnotationNamespace('Doctrine\ORM\Mapping\\');
            }
            $reader = new \Doctrine\Common\Annotations\CachedReader(new \Doctrine\Common\Annotations\IndexedReader($reader), new ArrayCache());
        } else {
            $reader = new \Doctrine\Common\Annotations\AnnotationReader();
            if ($alias) {
                $reader->setAnnotationNamespaceAlias('Doctrine\ORM\Mapping\\', $alias);
            } else {
                $reader->setDefaultAnnotationNamespace('Doctrine\ORM\Mapping\\');
            }
        }
        \Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__ . "/../../../lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");

        return new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, (array) $paths);
    }

    /**
     * Creates an EntityManager for testing purposes.
     *
     * NOTE: The created EntityManager will have its dependant DBAL parts completely
     * mocked out using a DriverMock, ConnectionMock, etc. These mocks can then
     * be configured in the tests to simulate the DBAL behavior that is desired
     * for a particular test,
     *
     * @param \Doctrine\DBAL\Connection|array    $conn
     * @param mixed                              $conf
     * @param \Doctrine\Common\EventManager|null $eventManager
     * @param bool                               $withSharedMetadata
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function _getTestEntityManager($conn = null, $conf = null, $eventManager = null, $withSharedMetadata = true)
    {
        $metadataCache = $withSharedMetadata ? self::getSharedMetadataCacheImpl() : new \Doctrine\Common\Cache\ArrayCache();

        $config = new \Doctrine\ORM\Configuration();

        $config->setMetadataCacheImpl($metadataCache);
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver([], true));
        $config->setQueryCacheImpl(self::getSharedQueryCacheImpl());
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace('Doctrine\Tests\Proxies');

        $config->setEntityNamespaces([
            'ZfcDatagridTest\DataSource\Doctrine2\Assets\Entity',
            \ZfcDatagridTest\DataSource\Doctrine2\Assets\Entity\Category::class,
        ]);

        if (null === $conn) {
            $conn = [
                'driverClass'  => \ZfcDatagridTest\DataSource\Doctrine2\Mocks\DriverMock::class,
                'wrapperClass' => \ZfcDatagridTest\DataSource\Doctrine2\Mocks\ConnectionMock::class,
                'user'         => 'john',
                'password'     => 'wayne',
            ];
        }

        if (is_array($conn)) {
            $conn = \Doctrine\DBAL\DriverManager::getConnection($conn, $config, $eventManager);
        }

        return \ZfcDatagridTest\DataSource\Doctrine2\Mocks\EntityManagerMock::create($conn, $config, $eventManager);
    }

    /**
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    private static function getSharedMetadataCacheImpl()
    {
        if (null === self::$_metadataCacheImpl) {
            self::$_metadataCacheImpl = new \Doctrine\Common\Cache\ArrayCache();
        }

        return self::$_metadataCacheImpl;
    }

    /**
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    private static function getSharedQueryCacheImpl()
    {
        if (null === self::$_queryCacheImpl) {
            self::$_queryCacheImpl = new \Doctrine\Common\Cache\ArrayCache();
        }

        return self::$_queryCacheImpl;
    }

    public function setUp()
    {
        $this->em = $this->_getTestEntityManager();

        parent::setUp();
    }
}
