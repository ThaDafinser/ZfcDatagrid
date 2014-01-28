<?php
namespace ZfcDatagridTest\Renderer\BootstrapTale;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\BootstrapTable;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\BootstrapTable\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{

    public function testGetName()
    {
        $renderer = new BootstrapTable\Renderer();
        
        $this->assertEquals('bootstrapTable', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new BootstrapTable\Renderer();
        
        $this->assertFalse($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new BootstrapTable\Renderer();
        
        $this->assertTrue($renderer->isHtml());
    }
}
