<?php
namespace ZfcDatagridTest\Column\Style;

use ZfcDatagrid\Filter;
// use ZfcDatagrid\Column\Type;
use PHPUnit_Framework_TestCase;

/**
 * @group Style
 * @covers ZfcDatagrid\Column\Type\AbstractStyle
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

    public function testByValue ()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        
        $this->assertEquals(array(), $this->style->getByValues());
        $this->style->setByValue($this->column, 'value', Filter::EQUAL);
        $this->assertEquals(array(
            'column' => $this->column,
            'value' => 'value',
            'operator' => Filter::EQUAL
        ), $this->style->getByValues());
        
        $this->assertFalse($style->isForAll());
        
        $row = array(
            array(
                'colName' => 'value2'
            )
        );
        $this->assertFalse($style->isApply($row));
        
        $row = array(
            array(
                'colName' => 'value'
            )
        );
        $this->assertTrue($style->isApply($row));
    }

    public function testGeneralStyle ()
    {
        /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        
        $this->assertEquals(array(), $this->style->getByValues());
        
        $this->assertTrue($style->isForAll());
        
        $row = array(
            array(
                'colName' => 'value2'
            )
        );
        $this->assertTrue($style->isApply($row));
    }
}