<?php
namespace ZfcDatagridTest\Column\Formatter;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Formatter;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Formatter\Email
 */
class EmailTest extends PHPUnit_Framework_TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = new Formatter\Email();

        $this->assertEquals([
            'jqGrid',
            'bootstrapTable',
        ], $formatter->getValidRendererNames());
    }

    public function testGetFormattedValue()
    {
        $col = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col->setUniqueId('myCol');

        $formatter = new Formatter\Email();
        $formatter->setRowData([
            'myCol' => 'name@example.com',
        ]);

        $this->assertEquals('<a href="mailto:name@example.com">name@example.com</a>', $formatter->getFormattedValue($col));
    }
}
