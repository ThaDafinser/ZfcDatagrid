<?php
namespace ZfcDatagridTest\DataSource\Doctrine2;

use Doctrine\ORM\QueryBuilder;
use ZfcDatagrid\DataSource\Doctrine2\Filter as FilterDoctrine2;

/**
 * @group DataSource
 * @covers \ZfcDatagrid\DataSource\Doctrine2\Filter
 */
class FilterTest extends AbstractDoctrine2Test
{
    /**
     *
     * @var FilterDoctrine2
     */
    private $filterDoctrine2;

    public function setUp()
    {
        parent::setUp();

        $qb                    = $this->em->createQueryBuilder();
        $this->filterDoctrine2 = new FilterDoctrine2($qb);
    }

    public function testBasic()
    {
        $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $this->filterDoctrine2->getQueryBuilder());

        // Test two filters
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '~myValue,123');

        $filter2 = new \ZfcDatagrid\Filter();
        $filter2->setFromColumn($this->colEdition, '~456');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);
        $filterDoctrine2->applyFilter($filter2);

        /* @var $where \Doctrine\ORM\Query\Expr\Andx */
        $where = $filterDoctrine2->getQueryBuilder()->getDQLPart('where');

        $this->assertEquals(2, $where->count());
        $this->assertInstanceOf(\Doctrine\ORM\Query\Expr\Andx::class, $where);

        $whereParts = $where->getParts();

        /* @var $wherePart1 \Doctrine\ORM\Query\Expr\Orx */
        $wherePart1 = $whereParts[0];

        $this->assertEquals(2, $wherePart1->count());
        $this->assertInstanceOf(\Doctrine\ORM\Query\Expr\Orx::class, $wherePart1);

        /* @var $wherePart2 \Doctrine\ORM\Query\Expr\Orx */
        $wherePart2 = $whereParts[1];

        $this->assertEquals(1, $wherePart2->count());
        $this->assertInstanceOf(\Doctrine\ORM\Query\Expr\Orx::class, $wherePart2);
    }

    /**
     *
     * @param  QueryBuilder                          $qb
     * @param  number                                $part
     * @return \Doctrine\ORM\Query\Expr\Comparison[]
     */
    private function getWhereParts(QueryBuilder $qb, $part = 0)
    {
        /* @var $where \Doctrine\ORM\Query\Expr\Andx */
        $where = $qb->getDQLPart('where');

        $whereParts = $where->getParts();

        $this->assertInstanceOf(\Doctrine\ORM\Query\Expr\Orx::class, $whereParts[$part]);

        return $whereParts[$part]->getParts();
    }

    /**
     *
     * @param FilterDoctrine2 $filter
     *
     * @return \Doctrine\ORM\Query\Parameter[]
     */
    private function getParameters(FilterDoctrine2 $filter)
    {
        return $filter->getQueryBuilder()->getParameters();
    }

    public function testLike()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '~myV\'alue,123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('LIKE', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('%myV\'alue%', $parameters[0]->getValue());

        $this->assertEquals('volume', $whereParts[1]->getLeftExpr());
        $this->assertEquals('LIKE', $whereParts[1]->getOperator());
        $this->assertEquals(':volume1', $whereParts[1]->getRightExpr());
        $this->assertEquals('%123%', $parameters[1]->getValue());
    }

    public function testLikeLeft()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '~%123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('LIKE', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('%123', $parameters[0]->getValue());
    }

    public function testLikeRight()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '~123%');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('LIKE', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('123%', $parameters[0]->getValue());
    }

    public function testNotLike()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '!~123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('NOT LIKE', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('%123%', $parameters[0]->getValue());
    }

    public function testNotLikeLeft()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '!~%123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('NOT LIKE', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('%123', $parameters[0]->getValue());
    }

    public function testNotLikeRight()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '!~123%');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('NOT LIKE', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('123%', $parameters[0]->getValue());
    }

    public function testEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '=123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('=', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('123', $parameters[0]->getValue());
    }

    public function testNotEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '!=a String');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('<>', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('a String', $parameters[0]->getValue());
    }

    public function testGreaterEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '>=123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('>=', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('123', $parameters[0]->getValue());
    }

    public function testGreater()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '>123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('>', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('123', $parameters[0]->getValue());
    }

    public function testLessEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '<=string');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('<=', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('string', $parameters[0]->getValue());
    }

    public function testLess()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '<123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume', $whereParts[0]->getLeftExpr());
        $this->assertEquals('<', $whereParts[0]->getOperator());
        $this->assertEquals(':volume0', $whereParts[0]->getRightExpr());
        $this->assertEquals('123', $parameters[0]->getValue());
    }

    public function testBetween()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->colVolumne, '789 <> 123');

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);

        $whereParts = $this->getWhereParts($filterDoctrine2->getQueryBuilder());
        $parameters = $this->getParameters($filterDoctrine2);

        $this->assertEquals('volume BETWEEN :volume0 AND :volume1', $whereParts[0]);
        $this->assertEquals('123', $parameters[0]->getValue());
        $this->assertEquals('789', $parameters[1]->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testException()
    {
        $filter = $this->getMockBuilder(\ZfcDatagrid\Filter::class)
            ->getMock();
        $filter->expects($this->any())
            ->method('getColumn')
            ->will($this->returnValue($this->colVolumne));
        $filter->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue([
            1,
        ]));
        $filter->expects($this->any())
            ->method('getOperator')
            ->will($this->returnValue(' () '));

        $filterDoctrine2 = clone $this->filterDoctrine2;
        $filterDoctrine2->applyFilter($filter);
    }
}
