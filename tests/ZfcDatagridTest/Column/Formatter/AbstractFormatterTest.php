<?php
namespace ZfcDatagridTest\Column\Formatter;

use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Formatter\AbstractFormatter
 */
class AbstractFormatterTest extends PHPUnit_Framework_TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Formatter\AbstractFormatter::class);

        $this->assertEquals([], $formatter->getValidRendererNames());

        $formatter->setValidRendererNames([
            'jqGrid',
        ]);
        $this->assertEquals([
            'jqGrid',
        ], $formatter->getValidRendererNames());
    }

    public function testRowData()
    {
        $formatter = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Formatter\AbstractFormatter::class);
        $this->assertEquals([], $formatter->getRowData());

        $data = [
            'myCol'  => 123,
            'myCol2' => 'text',
        ];

        $formatter->setRowData($data);
        $this->assertEquals($data, $formatter->getRowData());
    }

    public function testRendererName()
    {
        $formatter = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Formatter\AbstractFormatter::class);

        $this->assertNull($formatter->getRendererName());

        $formatter->setRendererName('jqGrid');
        $this->assertEquals('jqGrid', $formatter->getRendererName());
    }

    public function testIsApply()
    {
        $formatter = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Formatter\AbstractFormatter::class);
        $formatter->setValidRendererNames([
            'jqGrid',
        ]);

        $formatter->setRendererName('jqGrid');
        $this->assertTrue($formatter->isApply());

        $formatter->setRendererName('tcpdf');
        $this->assertFalse($formatter->isApply());
    }

    public function testFormat()
    {
        $formatter = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Formatter\AbstractFormatter::class);
        $formatter->setValidRendererNames([
            'jqGrid',
        ]);
        $data = [
            'myCol'  => 123,
            'myCol2' => 'text',
        ];
        $formatter->setRowData($data);

        $col = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col->setUniqueId('myCol');

        $formatter->setRendererName('tcpdf');
        $this->assertEquals(123, $formatter->format($col));

        //Null because the method is not implemented in AbstractClass!
        $formatter->setRendererName('jqGrid');
        $this->assertEquals(null, $formatter->format($col));
    }
}
