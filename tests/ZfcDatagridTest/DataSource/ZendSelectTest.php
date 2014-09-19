<?php
namespace ZfcDatagridTest\DataSource;

/**
 * All copyright here goes to Doctrine2!
 *
 * Copied from: https://github.com/doctrine/doctrine2/blob/master/tests/Doctrine/Tests/OrmTestCase.php
 */
use ZfcDatagrid\DataSource\ZendSelect;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column;
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
        $this->mockPlatform->expects($this->any())
            ->method('getIdentifierSeparator')
            ->will($this->returnValue('.'));

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
            $this->colEdition,
        ));
    }

    public function testConstruct()
    {
        $select = $this->getMock('Zend\Db\Sql\Select');

        $source = new ZendSelect($select);

        $this->assertInstanceOf('Zend\Db\Sql\Select', $source->getData());
        $this->assertEquals($select, $source->getData());

        $this->setExpectedException('InvalidArgumentException', 'A instance of Zend\Db\SqlSelect is needed to use this dataSource!');

        $source = new ZendSelect(array());
    }

    public function testExecuteException()
    {
        $select = $this->getMock('Zend\Db\Sql\Select');

        $source = new ZendSelect($select);

        $this->setExpectedException('Exception', 'Object "Zend\Db\Sql\Sql" is missing, please call setAdapter() first!');

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

    public function testJoinTable()
    {
        $this->markTestIncomplete('ZendSelect join table test');

        $col1 = new Column\Select('id', 'o');
        $col2 = new Column\Select('name', 'u');

        $select = new Select();
        $select->from(array(
            'o' => 'orders',
        ));
        $select->join(array(
            'u' => 'user',
        ), 'u.order = o.id');

        $source = new ZendSelect($select);
        $source->setAdapter($this->sql);
        $source->setColumns(array(
            $col1,
            $col2,
        ));
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
