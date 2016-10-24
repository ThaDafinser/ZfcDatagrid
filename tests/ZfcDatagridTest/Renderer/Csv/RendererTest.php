<?php
namespace ZfcDatagridTest\Renderer\Csv;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\Csv;

/**
 * @group Renderer
 * @covers \ZfcDatagrid\Renderer\Csv\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $renderer = new Csv\Renderer();

        $this->assertEquals('csv', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new Csv\Renderer();

        $this->assertTrue($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new Csv\Renderer();

        $this->assertFalse($renderer->isHtml());
    }
}
