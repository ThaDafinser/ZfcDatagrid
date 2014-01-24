<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Datagrid;
use Zend\Session\Container;

/**
 * @covers ZfcDatagrid\Datagrid
 */
class DatagridTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Datagrid
     */
    private $grid;

    public function setUp()
    {
        $config = include './config/module.config.php';
        $config = $config['ZfcDatagrid'];
        
        $cacheOptions = new \Zend\Cache\Storage\Adapter\MemoryOptions();
        $config['cache']['adapter']['name'] = 'Memory';
        $config['cache']['adapter']['options'] = $cacheOptions->toArray();
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent');
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->getMock('Zend\Http\PhpEnvironment\Request')));
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceManager');
        
        $this->grid = new Datagrid();
        $this->grid->setOptions($config);
        $this->grid->setMvcEvent($mvcEvent);
        $this->grid->setServiceLocator($serviceLocator);
    }

    public function testInit()
    {
        $this->assertFalse($this->grid->isInit());
        
        $this->grid->init();
        
        $this->assertTrue($this->grid->isInit());
    }

    public function testId()
    {
        $this->assertEquals('defaultGrid', $this->grid->getId());
        
        $this->grid->setId('myCustomId');
        $this->assertEquals('myCustomId', $this->grid->getId());
    }

    public function testSession()
    {
        $this->assertInstanceOf('Zend\Session\Container', $this->grid->getSession());
        $this->assertEquals('defaultGrid', $this->grid->getSession()
            ->getName());
        
        $this->grid->setSession(new Container('myName'));
        $this->assertInstanceOf('Zend\Session\Container', $this->grid->getSession());
        $this->assertEquals('myName', $this->grid->getSession()
            ->getName());
    }

    public function testCacheId()
    {
        $this->assertEquals('_defaultGrid', $this->grid->getCacheId());
        
        $this->grid->setCacheId('myCacheId');
        $this->assertEquals('myCacheId', $this->grid->getCacheId());
    }

    public function testServiceLocator()
    {
        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $this->grid->getServiceLocator());
        
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceManager');
        $this->grid->setServiceLocator($serviceLocator);
        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $this->grid->getServiceLocator());
        $this->assertEquals($serviceLocator, $this->grid->getServiceLocator());
    }

    public function testMvcEvent()
    {
        $this->assertInstanceOf('Zend\Mvc\MvcEvent', $this->grid->getMvcEvent());
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent');
        $this->grid->setMvcEvent($mvcEvent);
        $this->assertInstanceOf('Zend\Mvc\MvcEvent', $this->grid->getMvcEvent());
        $this->assertEquals($mvcEvent, $this->grid->getMvcEvent());
    }

    public function testRequest()
    {
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Request', $this->grid->getRequest());
    }

    public function testTranslator()
    {
        $this->assertFalse($this->grid->hasTranslator());
        
        $this->grid->setTranslator($this->getMock('Zend\I18n\Translator\Translator'));
        
        $this->assertTrue($this->grid->hasTranslator());
        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $this->grid->getTranslator());
    }

    public function testDataSourceArray()
    {
        $this->assertFalse($this->grid->hasDataSource());
        
        $this->grid->setDataSource(array());
        $this->assertTrue($this->grid->hasDataSource());
        $this->assertInstanceOf('ZfcDatagrid\DataSource\PhpArray', $this->grid->getDataSource());
        
        $source = $this->getMock('ZfcDatagrid\DataSource\PhpArray', array(), array(
            array()
        ));
        $this->grid->setDataSource($source);
        
        $this->setExpectedException('InvalidArgumentException');
        $this->grid->setDataSource(null);
    }

    public function testDataSourceZend()
    {
        // $this->assertFalse($this->datagrid->hasDataSource());
        
        // $this->datagrid->setDataSource(array());
        // $this->assertTrue($this->datagrid->hasDataSource());
        // $this->assertInstanceOf('ZfcDatagrid\DataSource\PhpArray', $this->datagrid->getDataSource());
        
        // $select = $this->getMock('Zend\Db\Sql\Select');
        // $this->datagrid->setDataSource($select);
        
        // $qb = $this->getMock('Doctrine\ORM\QueryBuilder', array(), array(
        // $this->getMock('Doctrine\ORM\EntityManager')
        // ));
        // $this->datagrid->setDataSource($qb);
    }

    public function testDefaultItemsPerRow()
    {
        $this->assertEquals(25, $this->grid->getDefaultItemsPerPage());
        
        $this->grid->setDefaultItemsPerPage(- 1);
        $this->assertEquals(- 1, $this->grid->getDefaultItemsPerPage());
    }

    public function testTitle()
    {
        $this->assertEquals('', $this->grid->getTitle());
        
        $this->grid->setTitle('My title');
        $this->assertEquals('My title', $this->grid->getTitle());
    }

    public function testParameters()
    {
        $this->assertFalse($this->grid->hasParameters());
        $this->assertEquals(array(), $this->grid->getParameters());
        
        $this->grid->addParameter('myPara', 'test');
        
        $this->assertEquals(array(
            'myPara' => 'test'
        ), $this->grid->getParameters());
        
        $this->grid->setParameters(array(
            'other' => 'blubb'
        ));
        $this->assertEquals(array(
            'other' => 'blubb'
        ), $this->grid->getParameters());
        $this->assertTrue($this->grid->hasParameters());
    }

    public function testUrl()
    {
        $this->assertEquals(null, $this->grid->getUrl());
        
        $this->grid->setUrl('/module/controller/action');
        $this->assertEquals('/module/controller/action', $this->grid->getUrl());
    }

    public function testExportRenderers()
    {
        $this->assertEquals(array(), $this->grid->getExportRenderers());
        
        $this->grid->setExportRenderers(array(
            'tcpdf' => 'PDF'
        ));
        
        $this->assertEquals(array(
            'tcpdf' => 'PDF'
        ), $this->grid->getExportRenderers());
    }

    public function testColumns()
    {
        $this->assertEquals(array(), $this->grid->getColumns());
        
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->expects($this->any())
            ->method('getUniqueId')
            ->will($this->returnValue('myUniqueId'));
        
        $this->grid->addColumn($col);
        
        $this->assertCount(1, $this->grid->getColumns());
        // @todo
        // $this->assertEquals($col, $this->datagrid->getColumnByUniqueId('myUniqueId'));
        
        $this->assertEquals(null, $this->grid->getColumnByUniqueId('notAvailable'));
    }

    public function testColumnArray()
    {
        $this->assertEquals(array(), $this->grid->getColumns());
        
        $column = array(
            'name' => 'Test',
            'index' => '123',
            'label' => 'blubb',
//             'select' => array(
//                 'table',
//                 'column'
//             )
        );
        
        $this->grid->addColumn($column);
        
        $this->assertCount(1, $this->grid->getColumns());
        
        $col = $this->grid->getColumnByUniqueId('123');
        $this->assertInstanceOf('ZfcDatagrid\Column\Select', $col);
        $this->assertEquals(null, $this->grid->getColumnByUniqueId('notAvailable'));
        $this->assertEquals('blubb', $col->getLabel());
        
//         $this->assertEquals('table', $col->getSelectPart1());
//         $this->assertEquals('column', $col->getSelectPart2());
    }

    public function testUserFilter()
    {
        $this->assertTrue($this->grid->isUserFilterEnabled());
        
        $this->grid->setUserFilterDisabled(true);
        $this->assertFalse($this->grid->isUserFilterEnabled());
    }

    public function testRowClickAction()
    {
        $this->assertFalse($this->grid->hasRowClickAction());
        
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        $this->grid->setRowClickAction($action);
        $this->assertEquals($action, $this->grid->getRowClickAction());
        $this->assertTrue($this->grid->hasRowClickAction());
    }

    public function testGetRendererName()
    {
        // Default on HTTP
        $this->assertEquals('bootstrapTable', $this->grid->getRendererName());
        
        // Default on CLI
        $_SERVER['argv'] = array(
            'foo.php',
            'foo' => 'baz',
            'bar'
        );
        $_ENV["FOO_VAR"] = "bar";
        
        $request = new \Zend\Console\Request();
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent');
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $this->grid->setMvcEvent($mvcEvent);
        $this->assertEquals('zendTable', $this->grid->getRendererName());
        
        // change default
        $this->grid->setRendererName('myRenderer');
        $this->assertEquals('myRenderer', $this->grid->getRendererName());
        
        // by HTTP request
        $_GET['rendererType'] = 'jqGrid';
        $request = new \Zend\Http\PhpEnvironment\Request();
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent');
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $this->grid->setMvcEvent($mvcEvent);
        $this->assertEquals('jqGrid', $this->grid->getRendererName());
    }

    public function testGetRenderer()
    {
        
        // $this->datagrid->getRenderer();
    }
}
