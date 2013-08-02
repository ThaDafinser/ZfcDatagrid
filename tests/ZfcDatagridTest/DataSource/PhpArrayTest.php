<?php
namespace ZfcDatagridTest\DataSource;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\DataSource\PhpArray;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Type;

/**
 * @group DataSource
 * @covers ZfcDatagrid\DataSource\PhpArray
 */
class PhpArrayTest extends PHPUnit_Framework_TestCase
{

    private $data;

    /**
     *
     * @var PhpArray
     */
    private $source;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $colVolumne;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $colEdition;

    public function setUp()
    {
        $data = array();
        $data[] = array(
            'volume' => 67,
            'edition' => 2
        );
        $data[] = array(
            'volume' => 86,
            'edition' => 1,
            'unneded' => 'blubb'
        );
        $data[] = array(
            'volume' => 85,
            'edition' => 6
        );
        $data[] = array(
            'volume' => 98,
            'edition' => 2
        );
        $data[] = array(
            'volume' => 86,
            'edition' => 6
        );
        $data[] = array(
            'volume' => 67,
            'edition' => 7
        );
        
        $this->data = $data;
        
        $col1 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col1->setUniqueId('volume');
        $col1->setSelect('volume');
        $col1->setType(new Type\Number());
        $this->colVolumne = $col1;
        
        $col2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col2->setUniqueId('edition');
        $col2->setSelect('edition');
        $this->colEdition = $col2;
        
        $source = new PhpArray($this->data);
        $source->setColumns(array(
            $col1,
            $col2
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
        $col1 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col1->setSelect('volume');
        $col1->setType(new Type\Number());
        
        $col2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col2->setSelect('edition');
        
        $source = new PhpArray($this->data);
        $source->addSortCondition($col1);
        $source->addSortCondition($col2, 'DESC');
        $source->execute();
        
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $source->getPaginatorAdapter());
    }

    public function testFilter()
    {
        $col1 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col1->setUniqueId('volume');
        $col1->setSelect('volume');
        $col1->setType(new Type\Number());
        
        $col2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col2->setSelect('edition');
        
        /*
         * LIKE
         */
        $filter = new Filter();
        $filter->setFromColumn($this->colVolumne, '~7');
        
        $source = new PhpArray($this->data);
        $source->addFilter($filter);
        $source->execute();
        
        $this->assertEquals(2, $source->getPaginatorAdapter()
            ->count());
        
//         /*
//          * LIKE LEFT
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '~%67');
        
//         $source = clone $this->source;
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(2, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * LIKE RIGHT
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '~6%');
        
//         $source = clone $this->source;
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(4, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * NOT LIKE
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '!~7');
        
//         $source = new PhpArray($this->data);
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(4, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * NOT LIKE LEFT
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '!~%67');
        
//         $source = clone $this->source;
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(4, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * NOT LIKE RIGHT
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '!~6%');
        
//         $source = clone $this->source;
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(2, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * EQUAL
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '=98');
        
//         $source = new PhpArray($this->data);
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(1, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * NOT EQUAL
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '!=98');
        
//         $source = new PhpArray($this->data);
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(5, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * GREATER EQUAL
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '>=98');
        
//         $source = new PhpArray($this->data);
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(1, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * GREATER
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '>98');
        
//         $source = new PhpArray($this->data);
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(0, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * LESS EQUAL
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '<=98');
        
//         $source = new PhpArray($this->data);
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(6, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * LESS
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '<98');
        
//         $source = new PhpArray($this->data);
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(5, $source->getPaginatorAdapter()
//             ->count());
        
//         /*
//          * LESS
//          */
//         $filter = new Filter();
//         $filter->setFromColumn($this->colVolumne, '50 <> 70');
        
//         $source = new PhpArray($this->data);
//         $source->addFilter($filter);
//         $source->execute();
        
//         $this->assertEquals(2, $source->getPaginatorAdapter()
//             ->count());
    }
}
