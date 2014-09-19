<?php
namespace ZfcDatagridTest\Column\Formatter;

use ZfcDatagrid\Column\Formatter;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Formatter\Link
 */
class LinkTest extends PHPUnit_Framework_TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = new Formatter\Link();

        $this->assertEquals(array(
            'jqGrid',
            'bootstrapTable',
        ), $formatter->getValidRendererNames());
    }

    public function testGetFormattedValue()
    {
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->setUniqueId('myCol');

        $formatter = new Formatter\Link();
        $formatter->setRowData(array(
            'myCol' => 'http://example.com',
        ));

        $this->assertEquals('<a href="http://example.com">http://example.com</a>', $formatter->getFormattedValue($col));
    }
}
