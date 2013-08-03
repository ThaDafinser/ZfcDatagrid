<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Datagrid;
use Zend\Session\Container;

/**
 * @covers ZfcDatagrid\Datagrid
 */
class DatagridTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Datagrid
     */
    private $datagrid;

    public function setUp()
    {
        $config = include './config/module.config.php';
        $config = $config['ZfcDatagrid'];
        
        $cacheOptions = new \Zend\Cache\Storage\Adapter\MemoryOptions();
        $config['cache']['adapter']['name'] = 'Memory';
        $config['cache']['adapter']['options'] = $cacheOptions->toArray();
        
        $this->datagrid = new Datagrid();
        $this->datagrid->setOptions($config);
    }

    public function testInit()
    {
        $this->assertFalse($this->datagrid->isInit());
        
        $this->datagrid->init();
        
        $this->assertTrue($this->datagrid->isInit());
    }

    public function testId()
    {
        $this->assertEquals('defaultGrid', $this->datagrid->getId());
        
        $this->datagrid->setId('myCustomId');
        $this->assertEquals('myCustomId', $this->datagrid->getId());
    }

    public function testSession()
    {
        $this->assertInstanceOf('Zend\Session\Container', $this->datagrid->getSession());
        $this->assertEquals('defaultGrid', $this->datagrid->getSession()
            ->getName());
        
        $this->datagrid->setSession(new Container('myName'));
        $this->assertInstanceOf('Zend\Session\Container', $this->datagrid->getSession());
        $this->assertEquals('myName', $this->datagrid->getSession()
            ->getName());
    }
}