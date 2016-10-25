<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Module;

/**
 * @covers \ZfcDatagrid\Module
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $module = new Module();

        $this->assertTrue(is_array($module->getConfig()));
        $this->assertCount(4, $module->getConfig());
        $this->assertArrayHasKey('ZfcDatagrid', $module->getConfig());
    }
}
