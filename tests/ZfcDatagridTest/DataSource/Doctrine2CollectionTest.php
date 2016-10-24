<?php
namespace ZfcDatagridTest\DataSource;

use Doctrine\Common\Collections\ArrayCollection;
use ZfcDatagrid\DataSource\Doctrine2Collection;
use ZfcDatagrid\Filter;
use ZfcDatagridTest\DataSource\Doctrine2\Assets\Entity\Category;

/**
 * @group DataSource
 *
 * @covers \ZfcDatagrid\DataSource\Doctrine2Collection
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
        $source->setColumns([
            $this->colVolumne,
            $this->colEdition,
        ]);

        $this->source = $source;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown data input: "instanceof stdClass"
     */
    public function testConstructException()
    {
        $source = new Doctrine2Collection(new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown data input: ""
     */
    public function testConstructExceptionClass()
    {
        $source = new Doctrine2Collection(null);
    }

    public function testGetData()
    {
        $source = new Doctrine2Collection($this->collection);

        $this->assertEquals($this->collection, $source->getData());
    }

    public function testEntityManager()
    {
        $em = $this->getMockBuilder(\Doctrine\ORM\EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = clone $this->source;
        $this->assertNull($source->getEntityManager());

        $source->setEntityManager($em);
        $this->assertSame($em, $source->getEntityManager());
    }

//     public function testExecute()
//     {
//         $em = $this->getMock(\Doctrine\ORM\EntityManager::class, array(), array(), '', false);

//         $source = clone $this->source;
//         $source->setEntityManager($em);

//         $source->addSortCondition($this->colVolumne);
//         $source->addSortCondition($this->colEdition, 'DESC');
//         $source->execute();

//         $this->assertInstanceOf(\Zend\Paginator\Adapter\ArrayAdapter::class, $source->getPaginatorAdapter());
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
