<?php
namespace ZfcDatagridTest\DataSource;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\DataSource\ZendSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

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
        $select = $this->getMock('Zend\Db\Sql\Select');
        
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
        $select = $this->getMock('Zend\Db\Sql\Select');
        
        $source = new ZendSelect($select);
        $source->setAdapter($this->sql);
        
        $this->assertInstanceOf('Zend\Db\Sql\Sql', $source->getAdapter());
        $this->assertEquals($this->sql, $source->getAdapter());
        
        $source = new ZendSelect($select);
        $source->setAdapter($this->adapter);
        
        $this->assertInstanceOf('Zend\Db\Sql\Sql', $source->getAdapter());
        
        $this->setExpectedException('InvalidArgumentException');
        
        $source = new ZendSelect($select);
        $source->setAdapter('something');
    }

    public function testExecute()
    {
        
    }
}
