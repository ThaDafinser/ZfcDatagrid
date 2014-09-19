<?php
namespace ZfcDatagridTest\Column\Style;

use ZfcDatagrid\Filter;
use PHPUnit_Framework_TestCase;
use Zend\Stdlib\ErrorHandler;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Style\AbstractStyle
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
        $this->column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->column->setUniqueId('colName');
    }

    public function testGeneralStyle()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');

        $this->assertEquals(array(), $style->getByValues());

        $this->assertFalse($style->hasByValues());

        $row = array(
            array(
                'colName' => 'value2',
            ),
        );
        $this->assertTrue($style->isApply($row));
    }

    public function testSetByValueException()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');

        ErrorHandler::start(E_USER_DEPRECATED);
        $style->setByValue($this->column, 'test');
        $err = ErrorHandler::stop();

        $this->assertInstanceOf('ErrorException', $err);
    }

    public function testStyleByValue()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');

        $this->assertEquals(array(), $style->getByValues());
        $style->addByValue($this->column, 'myApplyValue', Filter::EQUAL);
        $this->assertEquals(array(
            array(
                'column' => $this->column,
                'value' => 'myApplyValue',
                'operator' => Filter::EQUAL,
            ),
        ), $style->getByValues());

        $this->assertTrue($style->hasByValues());

        $row = array(
            $this->column->getUniqueId() => 'value2',
        );
        $this->assertFalse($style->isApply($row));

        $row = array(
            $this->column->getUniqueId() => 'myApplyValue',
        );
        $this->assertTrue($style->isApply($row));
    }

    public function testStyleByValueNotEqual()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');

        $this->assertEquals(array(), $style->getByValues());
        $style->addByValue($this->column, 'myApplyValue', Filter::NOT_EQUAL);
        $this->assertEquals(array(
            array(
                'column' => $this->column,
                'value' => 'myApplyValue',
                'operator' => Filter::NOT_EQUAL,
            ),
        ), $style->getByValues());

        $row = array(
            $this->column->getUniqueId() => 'notEqualValue',
        );
        $this->assertTrue($style->isApply($row));

        $row = array(
            $this->column->getUniqueId() => 'myApplyValue',
        );
        $this->assertFalse($style->isApply($row));
    }

    public function testStyleByValueOrOperator()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');

        $this->assertEquals(array(), $style->getByValues());
        $style->addByValue($this->column, 'myApplyValue', Filter::EQUAL);
        $style->addByValue($this->column, '2nd value', Filter::EQUAL);
        $this->assertEquals(array(
            array(
                'column' => $this->column,
                'value' => 'myApplyValue',
                'operator' => Filter::EQUAL,
            ),
            array(
                'column' => $this->column,
                'value' => '2nd value',
                'operator' => Filter::EQUAL,
            ),
        ), $style->getByValues());

        $row = array(
            $this->column->getUniqueId() => '2nd value',
        );
        $this->assertTrue($style->isApply($row));

        $row = array(
            $this->column->getUniqueId() => 'myApplyValue',
        );
        $this->assertTrue($style->isApply($row));

        $row = array(
            $this->column->getUniqueId() => 'another value',
        );
        $this->assertFalse($style->isApply($row));
    }

    public function testIsApplyAndOperatorDisplay()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        $style->setByValueOperator('AND');

        $style->addByValue($this->column, '23', Filter::EQUAL);
        $style->addByValue($this->column, '2nd value', Filter::NOT_EQUAL);

        $this->assertTrue($style->isApply(array(
            $this->column->getUniqueId() => '23',
        )));
    }

    public function testIsApplyAndOperatorNoDisplay()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        $style->setByValueOperator('AND');

        $style->addByValue($this->column, '23', Filter::EQUAL);
        $style->addByValue($this->column, '23', Filter::NOT_EQUAL);

        $this->assertFalse($style->isApply(array(
            $this->column->getUniqueId() => '23',
        )));
    }

    public function testSetByValueOperatorException()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');

        $this->setExpectedException('InvalidArgumentException');
        $style->setByValueOperator('XOR');
    }
}
