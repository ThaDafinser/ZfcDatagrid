<?php
namespace ZfcDatagridTest\Column\Formatter;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Column\Formatter;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Formatter\Image
 */
class ImageTest extends TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = new Formatter\Image();

        $this->assertEquals([
            'jqGrid',
            'bootstrapTable',
            'printHtml',
        ], $formatter->getValidRendererNames());
    }
}
