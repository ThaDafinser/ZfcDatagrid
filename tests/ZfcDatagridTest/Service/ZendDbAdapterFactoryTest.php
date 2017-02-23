<?php
namespace ZfcDatagridTest\Service;

use PHPUnit\Framework\TestCase;
use Zend\ServiceManager\ServiceManager;
use ZfcDatagrid\Service\ZendDbAdapterFactory;

/**
 * @covers \ZfcDatagrid\Service\ZendDbAdapterFactory
 */
class ZendDbAdapterFactoryTest extends TestCase
{
    private $config = [
        'zfcDatagrid_dbAdapter' => [
            'driver'   => 'Pdo_Sqlite',
            'database' => 'somewhere/testDb.sqlite',
        ],
    ];

    public function testCanCreateService()
    {
        $sm = new ServiceManager();
        $sm->setService('config', $this->config);

        $factory = new ZendDbAdapterFactory();
        $grid    = $factory->__invoke($sm, 'zfcDatagrid_dbAdapter');

        $this->assertInstanceOf(\Zend\Db\Adapter\Adapter::class, $grid);
    }
}
