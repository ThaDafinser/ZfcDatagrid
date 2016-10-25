<?php
namespace ZfcDatagridTest\DataSource;

/**
 * All copyright here goes to Doctrine2!
 *
 * Copied from: https://github.com/doctrine/doctrine2/blob/master/tests/Doctrine/Tests/OrmTestCase.php
 */
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use ZfcDatagrid\Column;
use ZfcDatagrid\DataSource\ZendSelect;
use ZfcDatagrid\Filter;

/**
 * @group DataSource
 * @covers \ZfcDatagrid\DataSource\ZendSelect
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

        $this->mockDriver     = $this->getMockBuilder(\Zend\Db\Adapter\Driver\DriverInterface::class)->getMock();
        $this->mockConnection = $this->getMockBuilder(\Zend\Db\Adapter\Driver\ConnectionInterface::class)->getMock();
        $this->mockDriver->expects($this->any())
            ->method('checkEnvironment')
            ->will($this->returnValue(true));
        $this->mockDriver->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->mockConnection));
        $this->mockPlatform = $this->getMockBuilder(\Zend\Db\Adapter\Platform\PlatformInterface::class)->getMock();
        $this->mockPlatform->expects($this->any())
            ->method('getIdentifierSeparator')
            ->will($this->returnValue('.'));

        $this->mockStatement = $this->getMockBuilder(\Zend\Db\Adapter\Driver\StatementInterface::class)->getMock();
        $this->mockDriver->expects($this->any())
            ->method('createStatement')
            ->will($this->returnValue($this->mockStatement));

        $this->adapter = new Adapter($this->mockDriver, $this->mockPlatform);

        $this->sql = new Sql($this->adapter, 'foo');

        $select = new Select();

        $this->source = new ZendSelect($select);
        $this->source->setAdapter($this->sql);
        $this->source->setColumns([
            $this->colVolumne,
            $this->colEdition,
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A instance of Zend\Db\SqlSelect is needed to use this dataSource!
     */
    public function testConstruct()
    {
        $select = $this->getMockBuilder(\Zend\Db\Sql\Select::class)->getMock();

        $source = new ZendSelect($select);

        $this->assertInstanceOf(\Zend\Db\Sql\Select::class, $source->getData());
        $this->assertEquals($select, $source->getData());

        $source = new ZendSelect([]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Object "Zend\Db\Sql\Sql" is missing, please call setAdapter() first!
     */
    public function testExecuteException()
    {
        $select = $this->getMockBuilder(\Zend\Db\Sql\Select::class)->getMock();

        $source = new ZendSelect($select);

        $source->execute();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAdapter()
    {
        $source = clone $this->source;

        $this->assertInstanceOf(\Zend\Db\Sql\Sql::class, $source->getAdapter());
        $this->assertEquals($this->sql, $source->getAdapter());

        $source->setAdapter($this->adapter);
        $this->assertInstanceOf(\Zend\Db\Sql\Sql::class, $source->getAdapter());

        $source->setAdapter('something');
    }

    public function testExecute()
    {
        $source = clone $this->source;

        $source->addSortCondition($this->colVolumne);
        $source->addSortCondition($this->colEdition, 'DESC');
        $source->execute();

        $this->assertInstanceOf(\Zend\Paginator\Adapter\DbSelect::class, $source->getPaginatorAdapter());
    }

    public function testJoinTable()
    {
        $this->markTestIncomplete('ZendSelect join table test');

        $col1 = new Column\Select('id', 'o');
        $col2 = new Column\Select('name', 'u');

        $select = new Select();
        $select->from([
            'o' => 'orders',
        ]);
        $select->join([
            'u' => 'user',
        ], 'u.order = o.id');

        $source = new ZendSelect($select);
        $source->setAdapter($this->sql);
        $source->setColumns([
            $col1,
            $col2,
        ]);
        $source->execute();

//         var_dump($source->getData()->getSqlString());
//         exit();
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
