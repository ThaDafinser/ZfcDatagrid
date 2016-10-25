<?php
namespace ZfcDatagridTest\DataSource;

use PHPUnit_Framework_TestCase;

/**
 * @covers \ZfcDatagrid\DataSource\AbstractDataSource
 */
class AbstractDataSourceTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \ZfcDatagrid\DataSource\AbstractDataSource
     */
    private $dsMock;

    public function setUp()
    {
        // if (defined('HPHP_VERSION') === true) {
        // $this->fail('HHVM Fatals');
        // }
        $this->dsMock = $this->getMockForAbstractClass(\ZfcDatagrid\DataSource\AbstractDataSource::class, [
            [],
        ], '', false);
    }

    public function testDefaults()
    {
        $ds = clone $this->dsMock;

        $this->assertEquals([], $ds->getColumns());
        $this->assertEquals([], $ds->getSortConditions());
        $this->assertEquals([], $ds->getFilters());
        $this->assertNull($ds->getPaginatorAdapter());
    }

    public function testColumn()
    {
        $ds = clone $this->dsMock;

        $col1 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col1->setUniqueId('test');
        $col2 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col2->setUniqueId('test2');
        $columns = [
            $col1->getUniqueId() => $col1,
            $col2->getUniqueId() => $col2,
        ];
        $ds->setColumns($columns);

        $this->assertArrayHasKey($col1->getUniqueId(), $ds->getColumns());
        $this->assertArrayHasKey($col2->getUniqueId(), $ds->getColumns());
        $this->assertCount(2, $ds->getColumns());
    }

    public function testSortCondition()
    {
        $ds = clone $this->dsMock;

        $col1 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col2 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);

        $ds->addSortCondition($col1, 'ASC');

        $this->assertEquals([
            [
                'column'        => $col1,
                'sortDirection' => 'ASC',
            ],
        ], $ds->getSortConditions());

        $ds->addSortCondition($col2, 'DESC');

        $this->assertEquals([
            [
                'column'        => $col1,
                'sortDirection' => 'ASC',
            ],
            [
                'column'        => $col2,
                'sortDirection' => 'DESC',
            ],
        ], $ds->getSortConditions());
    }

    public function testFilter()
    {
        $ds = clone $this->dsMock;

        $filter = $this->getMockBuilder(\ZfcDatagrid\Filter::class)
            ->getMock();
        $ds->addFilter($filter);

        $this->assertEquals([
            $filter,
        ], $ds->getFilters());
    }

    public function testPaginatorAdapter()
    {
        $ds = clone $this->dsMock;

        $adapter = $this->getMockBuilder(\Zend\Paginator\Adapter\ArrayAdapter::class)
            ->getMock();
        $ds->setPaginatorAdapter($adapter);

        $this->assertInstanceOf(\Zend\Paginator\Adapter\AdapterInterface::class, $ds->getPaginatorAdapter());
        $this->assertEquals($adapter, $ds->getPaginatorAdapter());
    }
}
