<?php
namespace ZfcDatagridTest\DataSource;

/**
 * All copyright here goes to Doctrine2!
 *
 * Copied from: https://github.com/doctrine/doctrine2/blob/master/tests/Doctrine/Tests/OrmTestCase.php
 */
use PHPUnit_Framework_TestCase;
use ZfcDatagrid\DataSource\ZendSelect;
use ZfcDatagrid\Filter;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

/**
 * @group DataSource
 * @covers ZfcDatagrid\DataSource\ZendSelect
 */
class ZendSelectTest extends DataSourceTestCase
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    /**
     * Sql object
     *
     * @var Sql
     */
    protected $sql = null;

    /**
     *
     * @var ZendSelect
     */
    protected $source;

    /**
     * https://github.com/zendframework/zf2/blob/master/tests/ZendTest/Db/Adapter/AdapterTest.php#L43
     * https://github.com/zendframework/zf2/blob/master/tests/ZendTest/Db/Sql/SqlTest.php#L26
     */
    public function setUp()
    {
        parent::setUp();
        
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
        
        $this->sql = new Sql($this->adapter, 'foo');
        
        $select = new Select();
        
        $this->source = new ZendSelect($select);
        $this->source->setAdapter($this->sql);
        $this->source->setColumns(array(
            $this->colVolumne,
            $this->colEdition
        ));
    }

    public function testConstruct()
    {
        $select = $this->getMock('Zend\Db\Sql\Select');
        
        $source = new ZendSelect($select);
        
        $this->assertInstanceOf('Zend\Db\Sql\Select', $source->getData());
        $this->assertEquals($select, $source->getData());
        
        $this->setExpectedException('InvalidArgumentException');
        
        $source = new ZendSelect(array());
    }

    public function testExecuteException()
    {
        $select = $this->getMock('Zend\Db\Sql\Select');
        
        $source = new ZendSelect($select);
        
        $this->setExpectedException('Exception');
        
        $source->execute();
    }

    public function testAdapter()
    {
        $source = clone $this->source;
        
        $this->assertInstanceOf('Zend\Db\Sql\Sql', $source->getAdapter());
        $this->assertEquals($this->sql, $source->getAdapter());
        
        $source->setAdapter($this->adapter);
        $this->assertInstanceOf('Zend\Db\Sql\Sql', $source->getAdapter());
        
        $this->setExpectedException('InvalidArgumentException');
        $source->setAdapter('something');
    }

    public function testExecute()
    {
        $source = clone $this->source;
        
        $source->addSortCondition($this->colVolumne);
        $source->addSortCondition($this->colEdition, 'DESC');
        $source->execute();
        
        $this->assertInstanceOf('Zend\Paginator\Adapter\DbSelect', $source->getPaginatorAdapter());
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
        
        // $this->assertEquals(2, $source->getPaginatorAdapter()
        // ->count());
    }
}
