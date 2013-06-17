<?php
namespace ZfcDatagridTest\Column;

use ZfcDatagrid\Column\DataPopulation;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use PHPUnit_Framework_TestCase;

class AbstractColumnTest extends PHPUnit_Framework_TestCase
{

    public function testGeneral ()
    {
        /* @var $column \ZfcDatagrid\Column\AbstractColumn */
        $column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        
        $this->assertEquals(5, $column->getWidth());
        $this->assertEquals(false, $column->isHidden());
        $this->assertEquals(false, $column->isIdentity());
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\AbstractType', $column->getType());
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\String', $column->getType());
        
        $this->assertEquals(false, $column->isTranslationEnabled());
        
        $column->setLabel('test');
        $this->assertEquals('test', $column->getLabel());
        
        $column->setUniqueId('unique_id');
        $this->assertEquals('unique_id', $column->getUniqueId());
        
        $column->setSelect('id', 'user');
        $this->assertEquals('id', $column->getSelectPart1());
        $this->assertEquals('user', $column->getSelectPart2());
        
        $column->setWidth(30);
        $this->assertEquals(30, $column->getWidth());
        $column->setWidth(50.53);
        $this->assertEquals(50.53, $column->getWidth());
        
        $column->setHidden(true);
        $this->assertEquals(true, $column->isHidden());
        $column->setHidden(false);
        $this->assertEquals(false, $column->isHidden());
        
        $column->setIdentity(true);
        $this->assertEquals(true, $column->isIdentity());
        $column->setIdentity(false);
        $this->assertEquals(false, $column->isIdentity());
    }

    public function testTypeStyle ()
    {
        /* @var $column \ZfcDatagrid\Column\AbstractColumn */
        $column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        
        $this->assertEquals(array(), $column->getStyles());
        $this->assertEquals(false, $column->hasStyles());
        
        $column->setType(new Type\Email());
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\AbstractType', $column->getType());
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\Email', $column->getType());
        
        $column->addStyle(new Style\Bold());
        $this->assertEquals(true, $column->hasStyles());
        $this->assertEquals(1, count($column->getStyles()));
        
        $style = $column->getStyles();
        $style = array_pop($style);
        $this->assertInstanceOf('ZfcDatagrid\Column\Style\Bold', $style);
        $this->assertInstanceOf('ZfcDatagrid\Column\Style\AbstractStyle', $style);
    }

    public function testSort ()
    {
        /* @var $column \ZfcDatagrid\Column\AbstractColumn */
        $column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        
        $this->assertEquals(true, $column->isUserSortEnabled());
        $this->assertEquals(false, $column->hasSortDefault());
        $this->assertEquals(array(), $column->getSortDefault());
        
        $this->assertEquals(false, $column->isSortActive());
        
        $column->setUserSortDisabled(true);
        $this->assertEquals(false, $column->isUserSortEnabled());
        $column->setUserSortDisabled(false);
        $this->assertEquals(true, $column->isUserSortEnabled());
        
        $column->setSortDefault(1, 'DESC');
        $this->assertEquals(array(
            'priority' => 1,
            'sortDirection' => 'DESC'
        ), $column->getSortDefault());
        $this->assertEquals(true, $column->hasSortDefault());
        
        $column->setSortActive('ASC');
        $this->assertEquals(true, $column->isSortActive());
        $this->assertEquals('ASC', $column->getSortActiveDirection());
        
        $column->setSortActive('DESC');
        $this->assertEquals(true, $column->isSortActive());
        $this->assertEquals('DESC', $column->getSortActiveDirection());
    }

    public function testFilter ()
    {
        /* @var $column \ZfcDatagrid\Column\AbstractColumn */
        $column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        
        $this->assertEquals(true, $column->isUserFilterEnabled());
        
        $this->assertEquals(false, $column->hasFilterDefaultValue());
        
        $this->assertEquals(Filter::LIKE, $column->getFilterDefaultOperation());
        $this->assertEquals('', $column->getFilterDefaultValue());
        
        $this->assertEquals(false, $column->hasFilterSelectOptions());
        $this->assertEquals(null, $column->getFilterSelectOptions());
        
        $this->assertEquals(false, $column->isFilterActive());
        $this->assertEquals('', $column->getFilterActiveValue());
        
        $column->setUserFilterDisabled(true);
        $this->assertEquals(false, $column->isUserFilterEnabled());
        $column->setUserFilterDisabled(false);
        $this->assertEquals(true, $column->isUserFilterEnabled());
        
        $column->setFilterDefaultValue('!=blubb');
        $this->assertEquals(true, $column->hasFilterDefaultValue());
        $this->assertEquals('!=blubb', $column->getFilterDefaultValue());
        
        $column->setFilterDefaultOperation(Filter::GREATER_EQUAL);
        $this->assertEquals(Filter::GREATER_EQUAL, $column->getFilterDefaultOperation());
        
        $column->setFilterSelectOptions(array(
            1 => 'one',
            2 => 'two'
        ));
        $this->assertEquals(3, count($column->getFilterSelectOptions()));
        $this->assertEquals(true, $column->hasFilterSelectOptions());
        
        $column->setFilterSelectOptions(array(
            1 => 'one',
            2 => 'two'
        ), false);
        $this->assertEquals(2, count($column->getFilterSelectOptions()));
        $this->assertEquals(true, $column->hasFilterSelectOptions());
        
        $column->setFilterActive('asdf');
        $this->assertEquals('asdf', $column->getFilterActiveValue());
        $this->assertEquals(true, $column->isFilterActive());
    }

    public function testSetGet ()
    {
        /* @var $column \ZfcDatagrid\Column\AbstractColumn */
        $column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        
        $column->setTranslationEnabled(true);
        $this->assertEquals(true, $column->isTranslationEnabled());
        $column->setTranslationEnabled(false);
        $this->assertEquals(false, $column->isTranslationEnabled());
        
        $this->assertEquals(false, $column->hasReplaceValues());
        $this->assertEquals(array(), $column->getReplaceValues());
        $column->setReplaceValues(array(
            1,
            2,
            3
        ));
        $this->assertEquals(true, $column->hasReplaceValues());
        $this->assertEquals(array(
            1,
            2,
            3
        ), $column->getReplaceValues());
        $this->assertEquals(true, $column->notReplacedGetEmpty());
        $column->setReplaceValues(array(
            1,
            2,
            3
        ), false);
        $this->assertEquals(true, $column->hasReplaceValues());
        $this->assertEquals(array(
            1,
            2,
            3
        ), $column->getReplaceValues());
        $this->assertEquals(false, $column->notReplacedGetEmpty());
        
        $this->assertEquals(array(), $column->getRendererParameters('jqgrid'));
        
        $column->setRendererParameter('key', 'value', 'someRenderer');
        $this->assertEquals(array(
            'key' => 'value'
        ), $column->getRendererParameters('someRenderer'));

        
        $object = new DataPopulation\Object();
        $object->setObject(new DataPopulation\Object\Gravatar());
        $this->assertEquals(false, $column->hasDataPopulation());
        $column->setDataPopulation($object);
        $this->assertEquals(true, $column->hasDataPopulation());
        $this->assertInstanceOf('ZfcDatagrid\Column\DataPopulation\Object', $column->getDataPopulation());
    }
    
    public function testException(){
        /* @var $column \ZfcDatagrid\Column\AbstractColumn */
        $column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        
        $object = new DataPopulation\Object();
        $this->setExpectedException('Exception');
        $column->setDataPopulation($object);
    }
}