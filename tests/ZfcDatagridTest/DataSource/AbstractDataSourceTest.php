<?php
namespace ZfcDatagridTest\DataSource;

use PHPUnit_Framework_TestCase;

/**
 * @covers ZfcDatagrid\DataSource\AbstractDataSource
 */
class AbstractDataSourceTest extends PHPUnit_Framework_TestCase
{

    public function testDefaults()
    {
        /* @var $ds \ZfcDatagrid\DataSource\AbstractDataSource */
        $ds = $this->getMockForAbstractClass('ZfcDatagrid\DataSource\AbstractDataSource', array(
            array()
        ), '', false);
        
        $this->assertEquals(array(), $ds->getColumns());
        $this->assertEquals(array(), $ds->getSortConditions());
        $this->assertEquals(array(), $ds->getFilters());
        $this->assertNull($ds->getPaginatorAdapter());
    }

    public function testColumn()
    {
        /* @var $ds \ZfcDatagrid\DataSource\AbstractDataSource */
        $ds = $this->getMockForAbstractClass('ZfcDatagrid\DataSource\AbstractDataSource', array(
            array()
        ), '', false);
        
        $col1 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col1->setUniqueId('test');
        $col2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col2->setUniqueId('test2');
        $columns = array(
            $col1->getUniqueId() => $col1,
            $col2->getUniqueId() => $col2
        );
        $ds->setColumns($columns);
        
        $this->assertArrayHasKey($col1->getUniqueId(), $ds->getColumns());
        $this->assertArrayHasKey($col2->getUniqueId(), $ds->getColumns());
        $this->assertCount(2, $ds->getColumns());
    }

    public function testSortCondition()
    {
        /* @var $ds \ZfcDatagrid\DataSource\AbstractDataSource */
        $ds = $this->getMockForAbstractClass('ZfcDatagrid\DataSource\AbstractDataSource', array(
            array()
        ), '', false);
        
        $col1 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        
        $ds->addSortCondition($col1, 'ASC');
        
        $this->assertEquals(array(
            array(
                'column' => $col1,
                'sortDirection' => 'ASC'
            )
        ), $ds->getSortConditions());
        
        $ds->addSortCondition($col2, 'DESC');
        
        $this->assertEquals(array(
            array(
                'column' => $col1,
                'sortDirection' => 'ASC'
            ),
            array(
                'column' => $col2,
                'sortDirection' => 'DESC'
            )
        ), $ds->getSortConditions());
    }

    public function testFilter()
    {
        /* @var $ds \ZfcDatagrid\DataSource\AbstractDataSource */
        $ds = $this->getMockForAbstractClass('ZfcDatagrid\DataSource\AbstractDataSource', array(
            array()
        ), '', false);
        
        $filter = $this->getMock('ZfcDatagrid\Filter');
        $ds->addFilter($filter);
        
        $this->assertEquals(array(
            $filter
        ), $ds->getFilters());
    }

    public function testPaginatorAdapter()
    {
        /* @var $ds \ZfcDatagrid\DataSource\AbstractDataSource */
        $ds = $this->getMockForAbstractClass('ZfcDatagrid\DataSource\AbstractDataSource', array(
            array()
        ), '', false);
        
        $adapter = $this->getMock('Zend\Paginator\Adapter\ArrayAdapter');
        $ds->setPaginatorAdapter($adapter);
        
        $this->assertInstanceOf('Zend\Paginator\Adapter\AdapterInterface', $ds->getPaginatorAdapter());
        $this->assertEquals($adapter, $ds->getPaginatorAdapter());
    }
}