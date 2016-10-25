<?php
namespace ZfcDatagridTest\DataSource\ZendSelect;

use PHPUnit_Framework_TestCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Like;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use ZfcDatagrid\DataSource\ZendSelect\Filter as FilterSelect;

/**
 * @group DataSource
 * @covers \ZfcDatagrid\DataSource\ZendSelect\Filter
 */
class FilterTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $column;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $column2;

    /**
     *
     * @var FilterSelect
     */
    private $filterSelect;

    public function setUp()
    {
        $this->column = $this->getMockBuilder(\ZfcDatagrid\Column\Select::class)->disableOriginalConstructor()->getMock();
        $this->column->method('getSelectPart1')
        ->willReturn('myCol');
        $this->column->method('getType')
        ->willReturn(new \ZfcDatagrid\Column\Type\PhpString());

        $this->column->setUniqueId('myCol');
        $this->column->setSelect('myCol');

        $this->column2 = $this->getMockBuilder(\ZfcDatagrid\Column\Select::class)->disableOriginalConstructor()->getMock();
        $this->column2->method('getSelectPart1')
        ->willReturn('myCol2');
        $this->column2->method('getType')
        ->willReturn(new \ZfcDatagrid\Column\Type\PhpString());

        $this->column2->setUniqueId('myCol2');
        $this->column2->setSelect('myCol2');

        $this->mockDriver     = $this->getMockBuilder(\Zend\Db\Adapter\Driver\DriverInterface::class)
            ->getMock();
        $this->mockConnection = $this->getMockBuilder(\Zend\Db\Adapter\Driver\ConnectionInterface::class)
            ->getMock();
        $this->mockDriver->expects($this->any())
            ->method('checkEnvironment')
            ->will($this->returnValue(true));
        $this->mockDriver->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->mockConnection));
        $this->mockPlatform  = $this->getMockBuilder(\Zend\Db\Adapter\Platform\PlatformInterface::class)
            ->getMock();
        $this->mockStatement = $this->getMockBuilder(\Zend\Db\Adapter\Driver\StatementInterface::class)
            ->getMock();
        $this->mockDriver->expects($this->any())
            ->method('createStatement')
            ->will($this->returnValue($this->mockStatement));

        $this->adapter = new Adapter($this->mockDriver, $this->mockPlatform);

        $sql = new Sql($this->adapter, 'foo');

        $select = new Select('myTable');
        $select->columns([
            'myCol',
            'myCol2',
        ]);

        $this->filterSelect = new FilterSelect($sql, $select);
    }

    public function testBasic()
    {
        $this->assertInstanceOf(\Zend\Db\Sql\Select::class, $this->filterSelect->getSelect());
        $this->assertInstanceOf(\Zend\Db\Sql\Sql::class, $this->filterSelect->getSql());

        // Test two filters
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~myValue,123');

        $filter2 = new \ZfcDatagrid\Filter();
        $filter2->setFromColumn($this->column2, '~myValue,123');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);
        $filterSelect->applyFilter($filter2);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();
        $this->assertEquals(2, count($predicates));
    }

    /**
     *
     * @param unknown $predicates
     * @param number  $part
     *
     * @return \Zend\Db\Sql\Predicate\Expression
     */
    private function getWherePart($predicates, $part = 0)
    {
        /* @var $predicateSet \Zend\Db\Sql\Predicate\PredicateSet */
        $predicateSet = $predicates[0][1];

        $pred      = $predicateSet->getPredicates();
        $where     = $pred[$part][1];
        $wherePred = $where->getPredicates();

        return $wherePred[0][1];
    }

    public function testLike()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~myValue,123');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));

        $like = $this->getWherePart($predicates, 0);
        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Like::class, $like);
        $this->assertEquals('%myValue%', $like->getLike());

        $like = $this->getWherePart($predicates, 1);
        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Like::class, $like);
        $this->assertEquals('%123%', $like->getLike());
    }

    public function testLikeLeft()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~%myValue,123');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $like = $this->getWherePart($predicates, 0);
        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Like::class, $like);
        $this->assertEquals('%myValue', $like->getLike());

        $like = $this->getWherePart($predicates, 1);
        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Like::class, $like);
        $this->assertEquals('%123', $like->getLike());
    }

    public function testLikeRight()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~myValue%');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $like = $this->getWherePart($predicates, 0);
        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Like::class, $like);
        $this->assertEquals('myValue%', $like->getLike());
    }

    public function testNotLike()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $notLike    = $this->getWherePart($predicates, 0);
        $parameters = $notLike->getParameters();

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Expression::class, $notLike);
        $this->assertEquals('NOT LIKE ?', $notLike->getExpression());
        $this->assertEquals('%myValue%', $parameters[0]);
    }

    public function testNotLikeLeft()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~%myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $notLike    = $this->getWherePart($predicates, 0);
        $parameters = $notLike->getParameters();

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Expression::class, $notLike);
        $this->assertEquals('NOT LIKE ?', $notLike->getExpression());
        $this->assertEquals('%myValue', $parameters[0]);
    }

    public function testNotLikeRight()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~myValue%');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $notLike    = $this->getWherePart($predicates, 0);
        $parameters = $notLike->getParameters();

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Expression::class, $notLike);
        $this->assertEquals('NOT LIKE ?', $notLike->getExpression());
        $this->assertEquals('myValue%', $parameters[0]);
    }

    public function testEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '=myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $operator = $this->getWherePart($predicates, 0);

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Operator::class, $operator);
        $this->assertEquals(Operator::OP_EQ, $operator->getOperator());
        $this->assertEquals('myCol', $operator->getLeft());
        $this->assertEquals('myValue', $operator->getRight());
    }

    public function testNotEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!=myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $operator = $this->getWherePart($predicates, 0);

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Operator::class, $operator);
        $this->assertEquals(Operator::OP_NE, $operator->getOperator());
        $this->assertEquals('myCol', $operator->getLeft());
        $this->assertEquals('myValue', $operator->getRight());
    }

    public function testGreaterEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '>=myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $operator = $this->getWherePart($predicates, 0);

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Operator::class, $operator);
        $this->assertEquals(Operator::OP_GTE, $operator->getOperator());
        $this->assertEquals('myCol', $operator->getLeft());
        $this->assertEquals('myValue', $operator->getRight());
    }

    public function testGreater()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '>myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $operator = $this->getWherePart($predicates, 0);

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Operator::class, $operator);
        $this->assertEquals(Operator::OP_GT, $operator->getOperator());
        $this->assertEquals('myCol', $operator->getLeft());
        $this->assertEquals('myValue', $operator->getRight());
    }

    public function testLessEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '<=myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $operator = $this->getWherePart($predicates, 0);

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Operator::class, $operator);
        $this->assertEquals(Operator::OP_LTE, $operator->getOperator());
        $this->assertEquals('myCol', $operator->getLeft());
        $this->assertEquals('myValue', $operator->getRight());
    }

    public function testLess()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '<myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $operator = $this->getWherePart($predicates, 0);

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Operator::class, $operator);
        $this->assertEquals(Operator::OP_LT, $operator->getOperator());
        $this->assertEquals('myCol', $operator->getLeft());
        $this->assertEquals('myValue', $operator->getRight());
    }

    public function testBetween()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '3 <> myValue');

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);

        $select = $filterSelect->getSelect();
        /* @var $where \Zend\Db\Sql\Where */
        $where = $select->getRawState('where');

        $predicates = $where->getPredicates();

        $operator = $this->getWherePart($predicates, 0);

        $this->assertInstanceOf(\Zend\Db\Sql\Predicate\Between::class, $operator);
        $this->assertEquals('myCol', $operator->getIdentifier());
        $this->assertEquals('3', $operator->getMinValue());
        $this->assertEquals('myValue', $operator->getMaxValue());
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
            ->will($this->returnValue($this->column));
        $filter->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue([
            1,
        ]));
        $filter->expects($this->any())
            ->method('getOperator')
            ->will($this->returnValue(' () '));

        $filterSelect = clone $this->filterSelect;
        $filterSelect->applyFilter($filter);
    }
}
