<?php
namespace ZfcDatagridTest\DataSource;

use ZfcDatagrid\DataSource\PhpArray;
use ZfcDatagrid\Filter;

/**
 * @group DataSource
 *
 * @covers \ZfcDatagrid\DataSource\PhpArray
 */
class PhpArrayTest extends DataSourceTestCase
{
    /**
     *
     * @var PhpArray
     */
    private $source;

    public function setUp()
    {
        parent::setUp();

        $source = new PhpArray($this->data);
        $source->setColumns([
            $this->colVolumne,
            $this->colEdition,
        ]);

        $this->source = $source;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstruct()
    {
        $source = clone $this->source;

        $this->assertEquals($this->data, $source->getData());

        $source = new PhpArray(null);
    }

    public function testExecute()
    {
        $source = clone $this->source;

        $source->execute();

        $this->assertInstanceOf(\Zend\Paginator\Adapter\ArrayAdapter::class, $source->getPaginatorAdapter());
    }

    public function testFilter()
    {
        $source = clone $this->source;

        /*
         * LIKE
         */
        $filter = new Filter();
        $filter->setFromColumn($this->colVolumne, '~7');

        $source->addFilter($filter);
        $source->execute();

        $this->assertEquals(2, $source->getPaginatorAdapter()
            ->count());
    }

    public function testSortIsApply()
    {
        $source = clone $this->source;

        $source->addSortCondition($this->colVolumne);
        $source->addSortCondition($this->colEdition, 'DESC');

        $source->execute();
        $data = $source->getPaginatorAdapter()->getItems(0, 10);

        // test 1st column sort
        $this->assertEquals(67, $data[0]['volume']);
        $this->assertEquals(98, $data[5]['volume']);

        // test 2nd column sort
        $this->assertEquals(86, $data[3]['volume']);
        $this->assertEquals(86, $data[4]['volume']);

        $this->assertEquals(6, $data[3]['edition']);
        $this->assertEquals(1, $data[4]['edition']);
    }
}
