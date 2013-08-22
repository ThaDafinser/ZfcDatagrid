<?php
namespace ZfcDatagridTest\DataSource\ZendSelect;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\DataSource\ZendSelect\Filter as FilterSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Like;

/**
 * @group DataSource
 * @covers ZfcDatagrid\DataSource\ZendSelect\Filter
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
        $this->column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->column->setUniqueId('myCol');
        
        $this->column2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->column2->setUniqueId('myCol2');
        
        $this->mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $this->mockConnection = $this->getMock('Zend\Db\Adapter\Driver\ConnectionInterface');
        $this->mockDriver->expects($this->any())
            ->method('checkEnvironment')
            ->will($this->returnValue(true));
        $this->mockDriver->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->mockConnection));
        $this->mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');
        $this->mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $this->mockDriver->expects($this->any())
            ->method('createStatement')
            ->will($this->returnValue($this->mockStatement));
        
        $this->adapter = new Adapter($this->mockDriver, $this->mockPlatform);
        
        $sql = new Sql($this->adapter, 'foo');
        
        $select = new Select('myTable');
        $select->columns(array(
            'myCol',
            'myCol2'
        ));
        
        $this->filterSelect = new FilterSelect($sql, $select);
    }

    public function testBasic()
    {
        $this->assertInstanceOf('Zend\Db\Sql\Select', $this->filterSelect->getSelect());
        $this->assertInstanceOf('Zend\Db\Sql\Sql', $this->filterSelect->getSql());
        
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
     * @param number $part            
     *
     * @return \Zend\Db\Sql\Predicate\Predicate
     */
    private function getWherePart($predicates, $part = 0)
    {
        /* @var $predicateSet \Zend\Db\Sql\Predicate\PredicateSet */
        $predicateSet = $predicates[0][1];
        
        $pred = $predicateSet->getPredicates();
        $where = $pred[$part][1];
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
        
        // First nesting
        $this->assertEquals(2, count($predicates[0]));
        
        $like = $this->getWherePart($predicates, 0);
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Like', $like);
        $this->assertEquals('%myValue%', $like->getLike());
        
        $like = $this->getWherePart($predicates, 1);
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Like', $like);
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
        
        // First nesting
        $this->assertEquals(2, count($predicates[0]));
        
        $like = $this->getWherePart($predicates, 0);
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Like', $like);
    }
}
