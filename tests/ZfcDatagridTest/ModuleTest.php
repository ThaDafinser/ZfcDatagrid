<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Module;

/**
 * @covers ZfcDatagrid\Module
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{

    public function testGetAutoloaderConfig()
    {
        $module = new Module();

        $this->assertTrue(is_array($module->getAutoloaderConfig()));
        $this->assertCount(2, $module->getAutoloaderConfig());
        $this->assertArrayHasKey('Zend\Loader\StandardAutoloader', $module->getAutoloaderConfig());
        $this->assertArrayHasKey('Zend\Loader\ClassMapAutoloader', $module->getAutoloaderConfig());
    }

    public function testGetConfig()
    {
        $module = new Module();

        $this->assertTrue(is_array($module->getConfig()));
        $this->assertCount(9, $module->getConfig());
        $this->assertArrayHasKey('ZfcDatagrid', $module->getConfig());
    }

    public function testSetGetConsoleUsage()
    {
        $module = new Module();

        $console = $this->getMock('Zend\Console\Adapter\AbstractAdapter');

        $this->assertTrue(is_array($module->getConsoleUsage($console)));
        $this->assertCount(8, $module->getConsoleUsage($console));
    }

    public function testGetServiceConfig()
    {
        $module = new Module();

        $this->assertTrue(is_array($module->getServiceConfig()));

        $serviceConfig = $module->getServiceConfig();
        $this->assertCount(1, $serviceConfig);

        $this->assertArrayHasKey('factories', $serviceConfig);
        $this->assertCount(7, $serviceConfig['factories']);
    }
}
