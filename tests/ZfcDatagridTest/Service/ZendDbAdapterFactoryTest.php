<?php
namespace ZfcDatagridTest\Service;

use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;
use ZfcDatagrid\Service\ZendDbAdapterFactory;

/**
 * @covers \ZfcDatagrid\Service\ZendDbAdapterFactory
 */
class ZendDbAdapterFactoryTest extends PHPUnit_Framework_TestCase
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
        $grid    = $factory->createService($sm);

        $this->assertInstanceOf(\Zend\Db\Adapter\Adapter::class, $grid);
    }
}
