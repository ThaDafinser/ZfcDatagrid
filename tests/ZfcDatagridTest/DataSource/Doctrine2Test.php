<?php
namespace ZfcDatagridTest\DataSource;

use ZfcDatagrid\Filter;
use ZfcDatagrid\DataSource\Doctrine2;
use ZfcDatagridTest\DataSource\Doctrine2\AbstractDoctrine2Test;

/**
 * @group DataSource
 * @covers ZfcDatagrid\DataSource\Doctrine2
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
        $this->source->setColumns(array(
            $this->colVolumne,
            $this->colEdition,
            $this->colUserDisplayName,
        ));
    }

    public function testConstruct()
    {
        $source = clone $this->source;

        $this->assertInstanceOf('Doctrine\ORM\QueryBuilder', $source->getData());
        $this->assertSame($this->qb, $source->getData());

        $this->setExpectedException('InvalidArgumentException');
        $source = new Doctrine2(new \stdClass('something'));
    }

    public function testExecute()
    {
        $source = clone $this->source;

        $source->addSortCondition($this->colVolumne);
        $source->addSortCondition($this->colEdition, 'DESC');
        $source->execute();

        $this->assertInstanceOf('ZfcDatagrid\DataSource\Doctrine2\Paginator', $source->getPaginatorAdapter());
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

        $this->assertInstanceOf('Doctrine\ORM\Query\Expr\Andx', $source->getData()
            ->getDQLPart('where'));
    }
}
