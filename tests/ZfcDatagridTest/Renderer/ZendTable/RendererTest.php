<?php
namespace ZfcDatagridTest\Renderer\ZendTable;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\ZendTable;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\ZendTable\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{

    public function testGetName()
    {
        $renderer = new ZendTable\Renderer();
        
        $this->assertEquals('zendTable', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new ZendTable\Renderer();
        
        $this->assertFalse($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new ZendTable\Renderer();
        
        $this->assertFalse($renderer->isHtml());
    }
}
