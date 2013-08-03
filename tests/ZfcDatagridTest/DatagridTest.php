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
    private $datagrid;

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
        
        $this->datagrid = new Datagrid();
        $this->datagrid->setOptions($config);
        $this->datagrid->setMvcEvent($mvcEvent);
        $this->datagrid->setServiceLocator($serviceLocator);
    }

    public function testInit()
    {
        $this->assertFalse($this->datagrid->isInit());
        
        $this->datagrid->init();
        
        $this->assertTrue($this->datagrid->isInit());
    }

    public function testId()
    {
        $this->assertEquals('defaultGrid', $this->datagrid->getId());
        
        $this->datagrid->setId('myCustomId');
        $this->assertEquals('myCustomId', $this->datagrid->getId());
    }

    public function testSession()
    {
        $this->assertInstanceOf('Zend\Session\Container', $this->datagrid->getSession());
        $this->assertEquals('defaultGrid', $this->datagrid->getSession()
            ->getName());
        
        $this->datagrid->setSession(new Container('myName'));
        $this->assertInstanceOf('Zend\Session\Container', $this->datagrid->getSession());
        $this->assertEquals('myName', $this->datagrid->getSession()
            ->getName());
    }

    public function testCacheId()
    {
        $this->assertEquals('_defaultGrid', $this->datagrid->getCacheId());
        
        $this->datagrid->setCacheId('myCacheId');
        $this->assertEquals('myCacheId', $this->datagrid->getCacheId());
    }

    public function testServiceLocator()
    {
        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $this->datagrid->getServiceLocator());
        
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceManager');
        $this->datagrid->setServiceLocator($serviceLocator);
        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $this->datagrid->getServiceLocator());
        $this->assertEquals($serviceLocator, $this->datagrid->getServiceLocator());
    }

    public function testMvcEvent()
    {
        $this->assertInstanceOf('Zend\Mvc\MvcEvent', $this->datagrid->getMvcEvent());
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent');
        $this->datagrid->setMvcEvent($mvcEvent);
        $this->assertInstanceOf('Zend\Mvc\MvcEvent', $this->datagrid->getMvcEvent());
        $this->assertEquals($mvcEvent, $this->datagrid->getMvcEvent());
    }

    public function testRequest()
    {
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Request', $this->datagrid->getRequest());
    }

    public function testTranslator()
    {
        $this->assertFalse($this->datagrid->hasTranslator());
        
        $this->datagrid->setTranslator($this->getMock('Zend\I18n\Translator\Translator'));
        
        $this->assertTrue($this->datagrid->hasTranslator());
        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $this->datagrid->getTranslator());
    }

    public function testDataSourceArray()
    {
        $this->assertFalse($this->datagrid->hasDataSource());
        
        $this->datagrid->setDataSource(array());
        $this->assertTrue($this->datagrid->hasDataSource());
        $this->assertInstanceOf('ZfcDatagrid\DataSource\PhpArray', $this->datagrid->getDataSource());
        
        $source = $this->getMock('ZfcDatagrid\DataSource\PhpArray', array(), array(
            array()
        ));
        $this->datagrid->setDataSource($source);
        
        $this->setExpectedException('InvalidArgumentException');
        $this->datagrid->setDataSource(null);
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
        $this->assertEquals(25, $this->datagrid->getDefaultItemsPerPage());
        
        $this->datagrid->setDefaultItemsPerPage(- 1);
        $this->assertEquals(- 1, $this->datagrid->getDefaultItemsPerPage());
    }

    public function testTitle()
    {
        $this->assertEquals('', $this->datagrid->getTitle());
        
        $this->datagrid->setTitle('My title');
        $this->assertEquals('My title', $this->datagrid->getTitle());
    }

    public function testParameters()
    {
        $this->assertFalse($this->datagrid->hasParameters());
        $this->assertEquals(array(), $this->datagrid->getParameters());
        
        $this->datagrid->addParameter('myPara', 'test');
        
        $this->assertEquals(array(
            'myPara' => 'test'
        ), $this->datagrid->getParameters());
        
        $this->datagrid->setParameters(array(
            'other' => 'blubb'
        ));
        $this->assertEquals(array(
            'other' => 'blubb'
        ), $this->datagrid->getParameters());
        $this->assertTrue($this->datagrid->hasParameters());
    }

    public function testUrl()
    {
        $this->assertEquals(null, $this->datagrid->getUrl());
        
        $this->datagrid->setUrl('/module/controller/action');
        $this->assertEquals('/module/controller/action', $this->datagrid->getUrl());
    }

    public function testExportRenderers()
    {
        $this->assertEquals(array(), $this->datagrid->getExportRenderers());
        
        $this->datagrid->setExportRenderers(array(
            'tcpdf' => 'PDF'
        ));
        
        $this->assertEquals(array(
            'tcpdf' => 'PDF'
        ), $this->datagrid->getExportRenderers());
    }

    public function testColumns()
    {
        $this->assertEquals(array(), $this->datagrid->getColumns());
        
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->expects($this->any())
            ->method('getUniqueId')
            ->will($this->returnValue('myUniqueId'));
        
        $this->datagrid->addColumn($col);
        
        $this->assertCount(1, $this->datagrid->getColumns());
        // @todo
        // $this->assertEquals($col, $this->datagrid->getColumnByUniqueId('myUniqueId'));
        
        $this->assertEquals(null, $this->datagrid->getColumnByUniqueId('notAvailable'));
    }

    public function testUserFilter()
    {
        $this->assertTrue($this->datagrid->isUserFilterEnabled());
        
        $this->datagrid->setUserFilterDisabled(true);
        $this->assertFalse($this->datagrid->isUserFilterEnabled());
    }

    public function testRowClickAction()
    {
        $this->assertFalse($this->datagrid->hasRowClickAction());
        
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        $this->datagrid->setRowClickAction($action);
        $this->assertEquals($action, $this->datagrid->getRowClickAction());
        $this->assertTrue($this->datagrid->hasRowClickAction());
    }

    public function testGetRendererName()
    {
        // Default on HTTP
        $this->assertEquals('bootstrapTable', $this->datagrid->getRendererName());
        
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
        $this->datagrid->setMvcEvent($mvcEvent);
        $this->assertEquals('zendTable', $this->datagrid->getRendererName());
        
        // change default
        $this->datagrid->setRenderer('myRenderer');
        $this->assertEquals('myRenderer', $this->datagrid->getRendererName());
        
        // by HTTP request
        $_GET['rendererType'] = 'jqGrid';
        $request = new \Zend\Http\PhpEnvironment\Request();
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent');
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $this->datagrid->setMvcEvent($mvcEvent);
        $this->assertEquals('jqGrid', $this->datagrid->getRendererName());
    }

    public function testGetRenderer()
    {
        
        // $this->datagrid->getRenderer();
    }
}
