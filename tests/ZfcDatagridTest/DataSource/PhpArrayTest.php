<?php
namespace ZfcDatagridTest\DataSource;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\DataSource\PhpArray;
use ZfcDatagrid\Filter;

/**
 * @group DataSource
 *
 * @covers ZfcDatagrid\DataSource\PhpArray
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
        $source->setColumns(array(
            $this->colVolumne,
            $this->colEdition
        ));
        
        $this->source = $source;
    }

    public function testConstruct()
    {
        $source = new PhpArray($this->data);
        
        $this->assertEquals($this->data, $source->getData());
        
        $this->setExpectedException('InvalidArgumentException');
        
        $source = new PhpArray(null);
    }

    public function testExecute()
    {
        $source = new PhpArray($this->data);
        // $source = clone $this->source;
        
        $source->addSortCondition($this->colVolumne);
        $source->addSortCondition($this->colEdition, 'DESC');
        $source->execute();
        
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $source->getPaginatorAdapter());
    }

    public function testFilter()
    {
        $source = clone $this->source;
        
        /*
         * LIKE
         */
        $filter = new Filter();
        $filter->setFromColumn($this->colVolumne, '~7');
        
        // $source = new PhpArray($this->data);
        $source->addFilter($filter);
        $source->execute();
        
        $this->assertEquals(2, $source->getPaginatorAdapter()
            ->count());
    }
}
