<?php
namespace ZfcDatagridTest\Column\Formatter;

use ZfcDatagrid\Column\Formatter;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Formatter\Image
 */
class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = new Formatter\Image();

        $this->assertEquals(array(
            'jqGrid',
            'bootstrapTable',
            'printHtml',
        ), $formatter->getValidRendererNames());
    }
}
