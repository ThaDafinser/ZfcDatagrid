<?php
namespace ZfcDatagridTest\Column\Formatter;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Formatter;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Formatter\FileSize
 */
class FileSizeTest extends PHPUnit_Framework_TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = new Formatter\FileSize();

        $this->assertEquals([], $formatter->getValidRendererNames());

        $formatter->setRendererName('something');

        // Always true!
        $this->assertTrue($formatter->isApply());
    }

    public function testGetFormattedValue()
    {
        $col = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col->setUniqueId('myCol');

        $formatter = new Formatter\FileSize();

        $formatter->setRowData([
            'myCol' => null,
        ]);
        $this->assertNull($formatter->getFormattedValue($col));

        $formatter->setRowData([
            'myCol' => '',
        ]);
        $this->assertEquals('', $formatter->getFormattedValue($col));

        $formatter->setRowData([
            'myCol' => null,
        ]);
        $this->assertNull($formatter->getFormattedValue($col));

        $formatter->setRowData([
            'myCol' => 1,
        ]);
        $this->assertEquals('1.00 B', $formatter->getFormattedValue($col));

        $formatter->setRowData([
            'myCol' => 1024,
        ]);
        $this->assertEquals('1.00 KB', $formatter->getFormattedValue($col));

        $formatter->setRowData([
            'myCol' => 1030,
        ]);
        $this->assertEquals('1.01 KB', $formatter->getFormattedValue($col));

        $formatter->setRowData([
            'myCol' => 1048576,
        ]);
        $this->assertEquals('1.00 MB', $formatter->getFormattedValue($col));

        $formatter->setRowData([
        'myCol' => 1073741824,
        ]);
        $this->assertEquals('1.00 GB', $formatter->getFormattedValue($col));
    }
}
