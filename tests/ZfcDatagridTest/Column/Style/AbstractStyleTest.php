<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Filter;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\AbstractStyle
 */
class AbstractStyleTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $column;

    public function setUp()
    {
        $this->column = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $this->column->setUniqueId('colName');
    }

    public function testGeneralStyle()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);

        $this->assertEquals([], $style->getByValues());

        $this->assertFalse($style->hasByValues());

        $row = [
            [
                'colName' => 'value2',
            ],
        ];
        $this->assertTrue($style->isApply($row));
    }

    public function testStyleByValue()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);

        $this->assertEquals([], $style->getByValues());
        $style->addByValue($this->column, 'myApplyValue', Filter::EQUAL);
        $this->assertEquals([
            [
                'column'   => $this->column,
                'value'    => 'myApplyValue',
                'operator' => Filter::EQUAL,
            ],
        ], $style->getByValues());

        $this->assertTrue($style->hasByValues());

        $row = [
            $this->column->getUniqueId() => 'value2',
        ];
        $this->assertFalse($style->isApply($row));

        $row = [
            $this->column->getUniqueId() => 'myApplyValue',
        ];
        $this->assertTrue($style->isApply($row));
    }

    public function testStyleByValueNotEqual()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);

        $this->assertEquals([], $style->getByValues());
        $style->addByValue($this->column, 'myApplyValue', Filter::NOT_EQUAL);
        $this->assertEquals([
            [
                'column'   => $this->column,
                'value'    => 'myApplyValue',
                'operator' => Filter::NOT_EQUAL,
            ],
        ], $style->getByValues());

        $row = [
            $this->column->getUniqueId() => 'notEqualValue',
        ];
        $this->assertTrue($style->isApply($row));

        $row = [
            $this->column->getUniqueId() => 'myApplyValue',
        ];
        $this->assertFalse($style->isApply($row));
    }

    public function testStyleByValueOrOperator()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);

        $this->assertEquals([], $style->getByValues());
        $style->addByValue($this->column, 'myApplyValue', Filter::EQUAL);
        $style->addByValue($this->column, '2nd value', Filter::EQUAL);
        $this->assertEquals([
            [
                'column'   => $this->column,
                'value'    => 'myApplyValue',
                'operator' => Filter::EQUAL,
            ],
            [
                'column'   => $this->column,
                'value'    => '2nd value',
                'operator' => Filter::EQUAL,
            ],
        ], $style->getByValues());

        $row = [
            $this->column->getUniqueId() => '2nd value',
        ];
        $this->assertTrue($style->isApply($row));

        $row = [
            $this->column->getUniqueId() => 'myApplyValue',
        ];
        $this->assertTrue($style->isApply($row));

        $row = [
            $this->column->getUniqueId() => 'another value',
        ];
        $this->assertFalse($style->isApply($row));
    }

    public function testIsApplyAndOperatorDisplay()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);
        $style->setByValueOperator('AND');

        $style->addByValue($this->column, '23', Filter::EQUAL);
        $style->addByValue($this->column, '2nd value', Filter::NOT_EQUAL);

        $this->assertTrue($style->isApply([
            $this->column->getUniqueId() => '23',
        ]));
    }

    public function testIsApplyAndOperatorNoDisplay()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);
        $style->setByValueOperator('AND');

        $style->addByValue($this->column, '23', Filter::EQUAL);
        $style->addByValue($this->column, '23', Filter::NOT_EQUAL);

        $this->assertFalse($style->isApply([
            $this->column->getUniqueId() => '23',
        ]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetByValueOperatorException()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);

        $style->setByValueOperator('XOR');
    }

    public function testStyleByColumn()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);

        $columnCompare = clone $this->column;
        $columnCompare->setUniqueId('columnCompare');

        $style->addByValue($this->column, $columnCompare, Filter::GREATER_EQUAL);
        $this->assertEquals([
            [
                'column'   => $this->column,
                'value'    => $columnCompare,
                'operator' => Filter::GREATER_EQUAL,
            ],
        ], $style->getByValues());

        $this->assertTrue($style->hasByValues());

        // Test lower value
        $row = [
            $this->column->getUniqueId()  => 5,
            $columnCompare->getUniqueId() => 15,
        ];
        $this->assertFalse($style->isApply($row));

        // Test greater value
        $row = [
            $this->column->getUniqueId()  => 15,
            $columnCompare->getUniqueId() => 10,
        ];
        $this->assertTrue($style->isApply($row));

        // Test row without compared column
        $row = [
            $this->column->getUniqueId() => 15,
        ];
        $this->assertTrue($style->isApply($row));
    }
}
