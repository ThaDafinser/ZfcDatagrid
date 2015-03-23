<?php
namespace ZfcDatagridTest\DataSource;

use ZfcDatagrid\DataSource\Doctrine2Collection;
use ZfcDatagrid\Filter;
use Doctrine\Common\Collections\ArrayCollection;
use ZfcDatagridTest\DataSource\Doctrine2\Assets\Entity\Category;

/**
 * @group DataSource
 *
 * @covers ZfcDatagrid\DataSource\Doctrine2Collection
 */
class Doctrine2CollectionTest extends DataSourceTestCase
{
    /**
     *
     * @var Doctrine2Collection
     */
    private $source;

    private $collection;

    public function setUp()
    {
        parent::setUp();

        $collection = new ArrayCollection();
        foreach ($this->data as $row) {
            $collection->add(new Category());
        }
        $this->collection = $collection;

        $source = new Doctrine2Collection($this->collection);
        $source->setColumns(array(
            $this->colVolumne,
            $this->colEdition,
        ));

        $this->source = $source;
    }

    public function testConstructException()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown data input: "instanceof stdClass"');
        $source = new Doctrine2Collection(new \stdClass());
    }

    public function testConstructExceptionClass()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown data input: ""');
        $source = new Doctrine2Collection(null);
    }

    public function testGetData()
    {
        $source = new Doctrine2Collection($this->collection);

        $this->assertEquals($this->collection, $source->getData());
    }

    public function testEntityManager()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $source = clone $this->source;
        $this->assertNull($source->getEntityManager());

        $source->setEntityManager($em);
        $this->assertSame($em, $source->getEntityManager());
    }

//     public function testExecute()
//     {
//         $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

//         $source = clone $this->source;
//         $source->setEntityManager($em);

//         $source->addSortCondition($this->colVolumne);
//         $source->addSortCondition($this->colEdition, 'DESC');
//         $source->execute();

//         $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $source->getPaginatorAdapter());
//     }

    // public function testFilter()
    // {
    // $source = clone $this->source;

    // /*
    // * LIKE
    // */
    // $filter = new Filter();
    // $filter->setFromColumn($this->colVolumne, '~7');

    // $source->addFilter($filter);
    // $source->execute();

    // $this->assertEquals(2, $source->getPaginatorAdapter()
    // ->count());
    // }
}
