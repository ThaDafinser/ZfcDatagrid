<?php
namespace ZfcDatagridTest\Column\Formatter;

use ZfcDatagrid\Column\Formatter;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Formatter\Email
 */
class EmailTest extends PHPUnit_Framework_TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = new Formatter\Email();

        $this->assertEquals(array(
            'jqGrid',
            'bootstrapTable',
        ), $formatter->getValidRendererNames());
    }

    public function testGetFormattedValue()
    {
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->setUniqueId('myCol');

        $formatter = new Formatter\Email();
        $formatter->setRowData(array(
            'myCol' => 'name@example.com',
        ));

        $this->assertEquals('<a href="mailto:name@example.com">name@example.com</a>', $formatter->getFormattedValue($col));
    }
}
