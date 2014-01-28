<?php
namespace ZfcDatagridTest\Renderer\JqGrid;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\JqGrid;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\JqGrid\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{

    public function testGetName()
    {
        $renderer = new JqGrid\Renderer();
        
        $this->assertEquals('jqGrid', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new JqGrid\Renderer();
        
        $this->assertFalse($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new JqGrid\Renderer();
        
        $this->assertTrue($renderer->isHtml());
    }
}
