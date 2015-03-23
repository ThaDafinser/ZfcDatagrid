<?php
namespace ZfcDatagridTest\DataSource\Doctrine2\Mocks;

/**
 * Mock class for Driver.
 *
 * @copyright https://github.com/doctrine/doctrine2/blob/master/tests/Doctrine/Tests/Mocks/DriverMock.php
 */
class DriverMock implements \Doctrine\DBAL\Driver
{
    /**
     *
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform null
     */
    private $_platformMock;

    /**
     *
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager null
     */
    private $_schemaManagerMock;

    /**
     * @ERROR!!!
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        return new DriverConnectionMock();
    }

    /**
     * @ERROR!!!
     */
    public function getDatabasePlatform()
    {
        if (! $this->_platformMock) {
            $this->_platformMock = new DatabasePlatformMock();
        }

        return $this->_platformMock;
    }

    /**
     * @ERROR!!!
     */
    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        if (null == $this->_schemaManagerMock) {
            return new SchemaManagerMock($conn);
        } else {
            return $this->_schemaManagerMock;
        }
    }

    /* MOCK API */

    /**
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return void
     */
    public function setDatabasePlatform(\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->_platformMock = $platform;
    }

    /**
     *
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $sm
     *
     * @return void
     */
    public function setSchemaManager(\Doctrine\DBAL\Schema\AbstractSchemaManager $sm)
    {
        $this->_schemaManagerMock = $sm;
    }

    /**
     * @ERROR!!!
     */
    public function getName()
    {
        return 'mock';
    }

    /**
     * @ERROR!!!
     */
    public function getDatabase(\Doctrine\DBAL\Connection $conn)
    {
        return;
    }
}
