<?php
namespace ZfcDatagridTest\Renderer\TCPDF;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Renderer\TCPDF;

/**
 * @group Renderer
 * @covers \ZfcDatagrid\Renderer\TCPDF\Renderer
 */
class RendererTest extends TestCase
{
    public function testGetName()
    {
        $renderer = new TCPDF\Renderer();

        $this->assertEquals('TCPDF', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new TCPDF\Renderer();

        $this->assertTrue($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new TCPDF\Renderer();

        $this->assertFalse($renderer->isHtml());
    }
}
