<?php
namespace ZfcDatagridTest\DataSource;

use ZfcDatagrid\DataSource\Doctrine2;
use ZfcDatagrid\Filter;
use ZfcDatagridTest\DataSource\Doctrine2\AbstractDoctrine2Test;

/**
 * @group DataSource
 * @covers \ZfcDatagrid\DataSource\Doctrine2
 */
class Doctrine2Test extends AbstractDoctrine2Test
{
    /**
     *
     * @var Doctrine2
     */
    protected $source;

    protected $qb;

    public function setUp()
    {
        parent::setUp();

        $this->qb = $this->em->createQueryBuilder();

        $this->source = new Doctrine2($this->qb);
        $this->source->setColumns([
            $this->colVolumne,
            $this->colEdition,
            $this->colUserDisplayName,
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstruct()
    {
        $source = clone $this->source;

        $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $source->getData());
        $this->assertSame($this->qb, $source->getData());

        $source = new Doctrine2(new \stdClass('something'));
    }

    public function testExecute()
    {
        $source = clone $this->source;

        $source->addSortCondition($this->colVolumne);
        $source->addSortCondition($this->colEdition, 'DESC');
        $source->execute();

        $this->assertInstanceOf(\ZfcDatagrid\DataSource\Doctrine2\Paginator::class, $source->getPaginatorAdapter());
    }

    public function testFilter()
    {
        $source = clone $this->source;

        $this->assertNull($source->getData()
            ->getDQLPart('where'));

        /*
         * LIKE
         */
        $filter = new Filter();
        $filter->setFromColumn($this->colUserDisplayName, '~7');

        $source->addFilter($filter);
        $source->execute();

        $this->assertInstanceOf(\Doctrine\ORM\Query\Expr\Andx::class, $source->getData()
            ->getDQLPart('where'));
    }
}
