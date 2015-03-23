<?php
namespace ZfcDatagridTest\Column\Formatter;

use ZfcDatagrid\Column\Formatter;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Formatter\AbstractFormatter
 */
class AbstractFormatterTest extends PHPUnit_Framework_TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = $this->getMockForAbstractClass('ZfcDatagrid\Column\Formatter\AbstractFormatter');

        $this->assertEquals(array(), $formatter->getValidRendererNames());

        $formatter->setValidRendererNames(array(
            'jqGrid',
        ));
        $this->assertEquals(array(
            'jqGrid',
        ), $formatter->getValidRendererNames());
    }

    public function testRowData()
    {
        $formatter = $this->getMockForAbstractClass('ZfcDatagrid\Column\Formatter\AbstractFormatter');
        $this->assertEquals(array(), $formatter->getRowData());

        $data = array(
            'myCol' => 123,
            'myCol2' => 'text',
        );

        $formatter->setRowData($data);
        $this->assertEquals($data, $formatter->getRowData());
    }

    public function testRendererName()
    {
        $formatter = $this->getMockForAbstractClass('ZfcDatagrid\Column\Formatter\AbstractFormatter');

        $this->assertNull($formatter->getRendererName());

        $formatter->setRendererName('jqGrid');
        $this->assertEquals('jqGrid', $formatter->getRendererName());
    }

    public function testIsApply()
    {
        $formatter = $this->getMockForAbstractClass('ZfcDatagrid\Column\Formatter\AbstractFormatter');
        $formatter->setValidRendererNames(array(
            'jqGrid',
        ));

        $formatter->setRendererName('jqGrid');
        $this->assertTrue($formatter->isApply());

        $formatter->setRendererName('tcpdf');
        $this->assertFalse($formatter->isApply());
    }

    public function testFormat()
    {
        $formatter = $this->getMockForAbstractClass('ZfcDatagrid\Column\Formatter\AbstractFormatter');
        $formatter->setValidRendererNames(array(
            'jqGrid',
        ));
        $data = array(
            'myCol' => 123,
            'myCol2' => 'text',
        );
        $formatter->setRowData($data);

        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->setUniqueId('myCol');

        $formatter->setRendererName('tcpdf');
        $this->assertEquals(123, $formatter->format($col));

        //Null because the method is not implemented in AbstractClass!
        $formatter->setRendererName('jqGrid');
        $this->assertEquals(null, $formatter->format($col));
    }
}
