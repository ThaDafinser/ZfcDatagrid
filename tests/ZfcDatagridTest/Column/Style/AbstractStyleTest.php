<?php
namespace ZfcDatagridTest\Column\Style;

use ZfcDatagrid\Filter;
use PHPUnit_Framework_TestCase;

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

    public function setUp ()
    {
        $this->column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->column->setUniqueId('colName');
    }

    public function testGeneralStyle ()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        
        $this->assertEquals(array(), $style->getByValues());
        
        $this->assertTrue($style->isForAll());
        
        $row = array(
            array(
                'colName' => 'value2'
            )
        );
        $this->assertTrue($style->isApply($row));
    }

    public function testStyleByValue ()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        
        $this->assertEquals(array(), $style->getByValues());
        $style->setByValue($this->column, 'myApplyValue', Filter::EQUAL);
        $this->assertEquals(array(
            array(
                'column' => $this->column,
                'value' => 'myApplyValue',
                'operator' => Filter::EQUAL
            )
        ), $style->getByValues());
        
        $this->assertFalse($style->isForAll());
        
        $row = array(
            $this->column->getUniqueId() => 'value2'
        );
        $this->assertFalse($style->isApply($row));
        
        $row = array(
            $this->column->getUniqueId() => 'myApplyValue'
        );
        $this->assertTrue($style->isApply($row));
    }

    public function testStyleByValueNotEqual ()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        
        $this->assertEquals(array(), $style->getByValues());
        $style->setByValue($this->column, 'myApplyValue', Filter::NOT_EQUAL);
        $this->assertEquals(array(
            array(
                'column' => $this->column,
                'value' => 'myApplyValue',
                'operator' => Filter::NOT_EQUAL
            )
        ), $style->getByValues());
        
        $this->assertFalse($style->isForAll());
        
        $row = array(
            $this->column->getUniqueId() => 'notEqualValue'
        );
        $this->assertTrue($style->isApply($row));
        
        $row = array(
            $this->column->getUniqueId() => 'myApplyValue'
        );
        $this->assertFalse($style->isApply($row));
    }

    public function testStyleByValueException ()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        
        $this->setExpectedException('Exception');
        $style->setByValue($this->column, 'myApplyValue', Filter::BETWEEN);
        $style->isApply(array(
            'some' => 'data'
        ));
    }
}