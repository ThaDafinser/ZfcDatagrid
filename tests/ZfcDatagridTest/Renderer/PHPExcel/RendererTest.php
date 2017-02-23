<?php
namespace ZfcDatagridTest\Renderer\PHPExcel;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Renderer\PHPExcel;

/**
 * @group Renderer
 * @covers \ZfcDatagrid\Renderer\PHPExcel\Renderer
 */
class RendererTest extends TestCase
{
    public function testGetName()
    {
        $renderer = new PHPExcel\Renderer();

        $this->assertEquals('PHPExcel', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new PHPExcel\Renderer();

        $this->assertTrue($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new PHPExcel\Renderer();

        $this->assertFalse($renderer->isHtml());
    }
}
