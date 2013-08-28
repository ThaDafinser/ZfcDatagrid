<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Module;

/**
 * @covers ZfcDatagrid\Module
 */
class ModuleTest extends PHPUnit_Framework_TestCase{
    
    public function testInterfaces(){
        $module = new Module();
        
        $this->assertInstanceOf('Zend\ModuleManager\Feature\AutoloaderProviderInterface', $module);
        $this->assertInstanceOf('Zend\ModuleManager\Feature\ConfigProviderInterface', $module);
        $this->assertInstanceOf('Zend\ModuleManager\Feature\ConsoleUsageProviderInterface', $module);
    }
    public function testGetAutoloaderConfig(){
        $module = new Module();
        
        $this->assertTrue(is_array($module->getAutoloaderConfig()));
        $this->assertCount(1, $module->getAutoloaderConfig());
        $this->assertArrayHasKey('Zend\Loader\StandardAutoloader', $module->getAutoloaderConfig());
    }
    
    public function testGetConfig(){
        $module = new Module();
        
        $this->assertTrue(is_array($module->getConfig()));
        $this->assertCount(9, $module->getConfig());
        $this->assertArrayHasKey('ZfcDatagrid', $module->getConfig());
        
    }
    
    public function testSetGetConsoleUsage(){
        $module = new Module();
        
        $console = $this->getMock('Zend\Console\Adapter\AbstractAdapter');
        
        $this->assertTrue(is_array($module->getConsoleUsage($console)));
        $this->assertCount(8, $module->getConsoleUsage($console));
    }
}