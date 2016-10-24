<?php
namespace ZfcDatagridTest\Column\Formatter;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Formatter;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Formatter\Image
 */
class ImageTest extends PHPUnit_Framework_TestCase
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
