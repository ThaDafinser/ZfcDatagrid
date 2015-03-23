<?php
namespace ZfcDatagridTest\Column;

use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Formatter;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\AbstractColumn
 */
class AbstractColumnTest extends PHPUnit_Framework_TestCase
{
    public function testGeneral()
    {
        /* @var $col \ZfcDatagrid\Column\AbstractColumn */
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');

        $this->assertEquals(5, $col->getWidth());
        $this->assertEquals(false, $col->isHidden());
        $this->assertEquals(false, $col->isIdentity());
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\AbstractType', $col->getType());
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\String', $col->getType());

        $this->assertEquals(false, $col->isTranslationEnabled());

        $col->setLabel('test');
        $this->assertEquals('test', $col->getLabel());

        $col->setUniqueId('unique_id');
        $this->assertEquals('unique_id', $col->getUniqueId());

        $col->setSelect('id', 'user');
        $this->assertEquals('id', $col->getSelectPart1());
        $this->assertEquals('user', $col->getSelectPart2());

        $col->setWidth(30);
        $this->assertEquals(30, $col->getWidth());
        $col->setWidth(50.53);
        $this->assertEquals(50.53, $col->getWidth());

        $col->setHidden(true);
        $this->assertEquals(true, $col->isHidden());
        $col->setHidden(false);
        $this->assertEquals(false, $col->isHidden());

        $col->setIdentity(true);
        $this->assertEquals(true, $col->isIdentity());
        $col->setIdentity(false);
        $this->assertEquals(false, $col->isIdentity());
    }

    public function testStyle()
    {
        /* @var $col \ZfcDatagrid\Column\AbstractColumn */
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');

        $this->assertEquals(array(), $col->getStyles());
        $this->assertEquals(false, $col->hasStyles());

        $col->addStyle(new Style\Bold());
        $this->assertEquals(true, $col->hasStyles());
        $this->assertEquals(1, count($col->getStyles()));

        $style = $col->getStyles();
        $style = array_pop($style);
        $this->assertInstanceOf('ZfcDatagrid\Column\Style\Bold', $style);
        $this->assertInstanceOf('ZfcDatagrid\Column\Style\AbstractStyle', $style);
    }

    public function testType()
    {
        /* @var $col \ZfcDatagrid\Column\AbstractColumn */
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');

        // DEFAULT
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\String', $col->getType());

        $col->setType(new Type\PhpArray());
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\AbstractType', $col->getType());
        $this->assertInstanceOf('ZfcDatagrid\Column\Type\PhpArray', $col->getType());

        $this->assertNull($col->getFormatter());
        $col->setType(new Type\Image());
        $this->assertInstanceOf('ZfcDatagrid\Column\Formatter\Image', $col->getFormatter());
    }

    public function testSort()
    {
        /* @var $col \ZfcDatagrid\Column\AbstractColumn */
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');

        $this->assertEquals(true, $col->isUserSortEnabled());
        $this->assertEquals(false, $col->hasSortDefault());
        $this->assertEquals(array(), $col->getSortDefault());

        $this->assertEquals(false, $col->isSortActive());

        $col->setUserSortDisabled(true);
        $this->assertEquals(false, $col->isUserSortEnabled());
        $col->setUserSortDisabled(false);
        $this->assertEquals(true, $col->isUserSortEnabled());

        $col->setSortDefault(1, 'DESC');
        $this->assertEquals(array(
            'priority' => 1,
            'sortDirection' => 'DESC',
        ), $col->getSortDefault());
        $this->assertEquals(true, $col->hasSortDefault());

        $col->setSortActive('ASC');
        $this->assertEquals(true, $col->isSortActive());
        $this->assertEquals('ASC', $col->getSortActiveDirection());

        $col->setSortActive('DESC');
        $this->assertEquals(true, $col->isSortActive());
        $this->assertEquals('DESC', $col->getSortActiveDirection());
    }

    public function testFilter()
    {
        /* @var $col \ZfcDatagrid\Column\AbstractColumn */
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');

        $this->assertEquals(true, $col->isUserFilterEnabled());

        $this->assertEquals(false, $col->hasFilterDefaultValue());

        $this->assertEquals(Filter::LIKE, $col->getFilterDefaultOperation());
        $this->assertEquals('', $col->getFilterDefaultValue());

        $this->assertEquals(false, $col->hasFilterSelectOptions());
        $this->assertEquals(null, $col->getFilterSelectOptions());

        $this->assertEquals(false, $col->isFilterActive());
        $this->assertEquals('', $col->getFilterActiveValue());

        $col->setUserFilterDisabled(true);
        $this->assertEquals(false, $col->isUserFilterEnabled());
        $col->setUserFilterDisabled(false);
        $this->assertEquals(true, $col->isUserFilterEnabled());

        $col->setFilterDefaultValue('!=blubb');
        $this->assertEquals(true, $col->hasFilterDefaultValue());
        $this->assertEquals('!=blubb', $col->getFilterDefaultValue());

        $col->setFilterDefaultOperation(Filter::GREATER_EQUAL);
        $this->assertEquals(Filter::GREATER_EQUAL, $col->getFilterDefaultOperation());

        $col->setFilterSelectOptions(array(
            1 => 'one',
            2 => 'two',
        ));
        $this->assertEquals(3, count($col->getFilterSelectOptions()));
        $this->assertEquals(true, $col->hasFilterSelectOptions());

        $col->setFilterSelectOptions(array(
            1 => 'one',
            2 => 'two',
        ), false);
        $this->assertEquals(2, count($col->getFilterSelectOptions()));
        $this->assertEquals(true, $col->hasFilterSelectOptions());

        $col->unsetFilterSelectOptions();
        $this->assertEquals(null, $col->getFilterSelectOptions());
        $this->assertEquals(false, $col->hasFilterSelectOptions());

        $col->setFilterActive('asdf');
        $this->assertEquals('asdf', $col->getFilterActiveValue());
        $this->assertEquals(true, $col->isFilterActive());
    }

    public function testSetGet()
    {
        /* @var $col \ZfcDatagrid\Column\AbstractColumn */
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');

        $col->setTranslationEnabled(true);
        $this->assertEquals(true, $col->isTranslationEnabled());
        $col->setTranslationEnabled(false);
        $this->assertEquals(false, $col->isTranslationEnabled());

        $this->assertEquals(false, $col->hasReplaceValues());
        $this->assertEquals(array(), $col->getReplaceValues());
        $col->setReplaceValues(array(
            1,
            2,
            3,
        ));
        $this->assertEquals(true, $col->hasReplaceValues());
        $this->assertEquals(array(
            1,
            2,
            3,
        ), $col->getReplaceValues());
        $this->assertEquals(true, $col->notReplacedGetEmpty());
        $col->setReplaceValues(array(
            1,
            2,
            3,
        ), false);
        $this->assertEquals(true, $col->hasReplaceValues());
        $this->assertEquals(array(
            1,
            2,
            3,
        ), $col->getReplaceValues());
        $this->assertEquals(false, $col->notReplacedGetEmpty());

        $this->assertEquals(array(), $col->getRendererParameters('jqGrid'));

        $col->setRendererParameter('key', 'value', 'someRenderer');
        $this->assertEquals(array(
            'key' => 'value',
        ), $col->getRendererParameters('someRenderer'));
    }

    public function testFormatter()
    {
        /* @var $col \ZfcDatagrid\Column\AbstractColumn */
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');

        // DEFAULT
        $this->assertNull($col->getFormatter());
        $this->assertFalse($col->hasFormatter());

        $col->setFormatter(new Formatter\Link());
        $this->assertTrue($col->hasFormatter());
        $this->assertInstanceOf('ZfcDatagrid\Column\Formatter\AbstractFormatter', $col->getFormatter());
        $this->assertInstanceOf('ZfcDatagrid\Column\Formatter\Link', $col->getFormatter());
    }

    public function testRowClick()
    {
        /* @var $col \ZfcDatagrid\Column\AbstractColumn */
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');

        $this->assertTrue($col->isRowClickEnabled());

        $col->setRowClickDisabled(true);
        $this->assertFalse($col->isRowClickEnabled());

        $col->setRowClickDisabled(false);
        $this->assertTrue($col->isRowClickEnabled());
    }
}
