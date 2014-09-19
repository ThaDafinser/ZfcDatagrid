<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Datagrid;
use Zend\Session\Container;
use Zend\Stdlib\ErrorHandler;
use ZfcDatagrid\Column;

/**
 * @group Datagrid
 * @covers ZfcDatagrid\Datagrid
 */
class DatagridTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Datagrid
     */
    private $grid;

    /**
     *
     * @var array
     */
    private $config;

    public function setUp()
    {
        $config = include './config/module.config.php';
        $config = $config['ZfcDatagrid'];

        $cacheOptions = new \Zend\Cache\Storage\Adapter\MemoryOptions();
        $config['cache']['adapter']['name'] = 'Memory';
        $config['cache']['adapter']['options'] = $cacheOptions->toArray();

        $this->config = $config;

        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent');
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->getMock('Zend\Http\PhpEnvironment\Request')));
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceManager');

        $this->grid = new Datagrid();
        $this->grid->setOptions($this->config);
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
        $grid = new Datagrid();

        $this->assertEquals('defaultGrid', $this->grid->getId());

        $grid->setId('myCustomId');
        $this->assertEquals('myCustomId', $grid->getId());
    }

    public function testSession()
    {
        $this->assertInstanceOf('Zend\Session\Container', $this->grid->getSession());
        $this->assertEquals('defaultGrid', $this->grid->getSession()
            ->getName());

        $session = new Container('myName');

        $this->grid->setSession($session);
        $this->assertInstanceOf('Zend\Session\Container', $this->grid->getSession());
        $this->assertSame($session, $this->grid->getSession());
        $this->assertEquals('myName', $this->grid->getSession()
            ->getName());
    }

    public function testCacheId()
    {
        $grid = new Datagrid();
        $sessionId = $grid->getSession()
            ->getManager()
            ->getId();

        $this->assertEquals(md5($sessionId.'_defaultGrid'), $this->grid->getCacheId());

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
        $grid = new Datagrid();
        $this->assertFalse($grid->hasDataSource());

        $grid->setDataSource(array());
        $this->assertTrue($grid->hasDataSource());
        $this->assertInstanceOf('ZfcDatagrid\DataSource\PhpArray', $grid->getDataSource());

        $source = $this->getMock('ZfcDatagrid\DataSource\PhpArray', array(), array(
            array(),
        ));
        $grid->setDataSource($source);
        $this->assertTrue($grid->hasDataSource());

        $this->setExpectedException('InvalidArgumentException');
        $grid->setDataSource(null);
    }

    public function testDataSourceZend()
    {
        // $this->assertFalse($this->grid->hasDataSource());

        // $this->grid->setDataSource(array());
        // $this->assertTrue($this->grid->hasDataSource());
        // $this->assertInstanceOf('ZfcDatagrid\DataSource\PhpArray', $this->grid->getDataSource());

        // $select = $this->getMock('Zend\Db\Sql\Select');
        // $this->grid->setDataSource($select);

        // $qb = $this->getMock('Doctrine\ORM\QueryBuilder', array(), array(
        // $this->getMock('Doctrine\ORM\EntityManager')
        // ));
        // $this->grid->setDataSource($qb);
    }

    public function testDataSourceZendSelect()
    {
        $grid = new Datagrid();

        $this->assertFalse($grid->hasDataSource());

        $select = $this->getMock('Zend\Db\Sql\Select', array(), array(), '', false);

        $platform = $this->getMock('Zend\Db\Adapter\Platform\Sqlite');
        $platform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('myPlatform'));

        $adapter = $this->getMock('Zend\Db\Adapter\Adapter', array(), array(), '', false);
        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platform));

        $grid->setDataSource($select, $adapter);
        $this->assertTrue($grid->hasDataSource());
        $this->assertInstanceOf('ZfcDatagrid\Datasource\ZendSelect', $grid->getDataSource());

        $this->setExpectedException('InvalidArgumentException', 'For "Zend\Db\Sql\Select" also a "Zend\Db\Adapter\Sql" or "Zend\Db\Sql\Sql" is needed.');
        $grid->setDataSource($select);
    }

    public function testDataSourceDoctrine()
    {
        $grid = new Datagrid();

        $this->assertFalse($grid->hasDataSource());

        $qb = $this->getMock('Doctrine\ORM\QueryBuilder', array(), array(), '', false);

        $grid->setDataSource($qb);
        $this->assertTrue($grid->hasDataSource());
        $this->assertInstanceOf('ZfcDatagrid\DataSource\Doctrine2', $grid->getDataSource());
    }

    public function testDataSourceDoctrineCollection()
    {
        $grid = new Datagrid();

        $this->assertFalse($grid->hasDataSource());

        $coll = $this->getMock('Doctrine\Common\Collections\ArrayCollection', array(), array(), '', false);
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $grid->setDataSource($coll, $em);
        $this->assertTrue($grid->hasDataSource());
        $this->assertInstanceOf('ZfcDatagrid\DataSource\Doctrine2Collection', $grid->getDataSource());

        $this->setExpectedException('InvalidArgumentException', 'If providing a Collection, also the Doctrine\ORM\EntityManager is needed as a second parameter');
        $grid->setDataSource($coll);
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
            'myPara' => 'test',
        ), $this->grid->getParameters());

        $this->grid->setParameters(array(
            'other' => 'blubb',
        ));
        $this->assertEquals(array(
            'other' => 'blubb',
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
        /*
         * NEVER define default export renderer -> because the user cant remove them after!
         */
        $this->assertEquals(array(), $this->grid->getExportRenderers());

        $this->grid->setExportRenderers(array(
            'tcpdf' => 'PDF',
        ));

        $this->assertEquals(array(
            'tcpdf' => 'PDF',
        ), $this->grid->getExportRenderers());
    }

    public function testAddColumn()
    {
        $this->assertEquals(array(), $this->grid->getColumns());

        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->expects($this->any())
            ->method('getUniqueId')
            ->will($this->returnValue('myUniqueId'));

        $this->grid->addColumn($col);

        $this->assertCount(1, $this->grid->getColumns());

        $this->assertEquals(null, $this->grid->getColumnByUniqueId('notAvailable'));
    }

    public function testAddColumnInvalidArgumentException()
    {
        $grid = new Datagrid();

        $this->setExpectedException('InvalidArgumentException', 'createColumn() supports only a config array or instanceof Column\AbstractColumn as a parameter');
        $grid->addColumn(null);
    }

    public function testAddColumnArrayFQN()
    {
        $grid = new Datagrid();
        $this->assertEquals(array(), $grid->getColumns());

        $column = array(
            'colType' => 'ZfcDatagrid\Column\Select',
            'label' => 'My label',
            'select' => array(
                'column' => 'myCol',
                'table' => 'myTable',
            ),
        );
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf('ZfcDatagrid\Column\Select', $col);
        $this->assertEquals('My label', $col->getLabel());
    }

    public function testAddColumnArrayInvalidColType()
    {
        $grid = new Datagrid();
        $this->assertEquals(array(), $grid->getColumns());

        $column = array(
            'colType' => 'ZfcDatagrid\Column\Unknown',
            'label' => 'My label',
        );

        $this->setExpectedException('InvalidArgumentException', 'Column type: "ZfcDatagrid\Column\Unknown" not found!');
        $grid->addColumn($column);
    }

    public function testAddColumnArraySelect()
    {
        $grid = new Datagrid();
        $this->assertEquals(array(), $grid->getColumns());

        $column = array(
            'label' => 'My label',
            'select' => array(
                'column' => 'myCol',
                'table' => 'myTable',
            ),
        );
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf('ZfcDatagrid\Column\Select', $col);
        $this->assertEquals('My label', $col->getLabel());
    }

    public function testAddColumnArraySelectInvalidArgumentException()
    {
        $grid = new Datagrid();
        $this->assertEquals(array(), $grid->getColumns());

        $column = array(
            'label' => 'My label',
        );
        $this->setExpectedException('InvalidArgumentException', 'For "ZfcDatagrid\Column\Select" the option select[column] must be defined!');
        $grid->addColumn($column);
    }

    public function testAddColumnArrayTypeAction()
    {
        $grid = new Datagrid();

        $column = array(
            'colType' => 'action',
            'label' => 'My action',
        );
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('action');
        $this->assertInstanceOf('ZfcDatagrid\Column\Action', $col);
        $this->assertEquals('My action', $col->getLabel());
    }

    public function testAddColumnArrayStyle()
    {
        $grid = new Datagrid();

        $bold = new Column\Style\Bold();

        $column = array(
            'select' => array(
                'column' => 'myCol',
                'table' => 'myTable',
            ),
            'styles' => array(
                $bold,
            ),
        );
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf('ZfcDatagrid\Column\Select', $col);

        $this->assertEquals(array(
            $bold,
        ), $col->getStyles());
    }

    public function testAddColumnArraySortDefaultMinimal()
    {
        $grid = new Datagrid();

        $column = array(
            'select' => array(
                'column' => 'myCol',
                'table' => 'myTable',
            ),
            'sortDefault' => 1,
        );
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf('ZfcDatagrid\Column\Select', $col);

        $this->assertEquals(array(
            'priority' => 1,
            'sortDirection' => 'ASC',
        ), $col->getSortDefault());
    }

    public function testAddColumnArraySortDefault()
    {
        $grid = new Datagrid();

        $column = array(
            'select' => array(
                'column' => 'myCol',
                'table' => 'myTable',
            ),
            'sortDefault' => array(
                1,
                'ASC',
            ),
        );
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf('ZfcDatagrid\Column\Select', $col);

        $this->assertEquals(array(
            'priority' => 1,
            'sortDirection' => 'ASC',
        ), $col->getSortDefault());
    }

    public function testSetColumn()
    {
        $grid = new Datagrid();

        $this->assertEquals(array(), $grid->getColumns());

        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->setUniqueId('myUniqueId');

        $col2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col2->setUniqueId('myUniqueId2');

        $grid->setColumns(array(
            $col,
            $col2,
        ));

        $this->assertCount(2, $grid->getColumns());
        $this->assertEquals($col, $grid->getColumnByUniqueId('myUniqueId'));
        $this->assertEquals($col2, $grid->getColumnByUniqueId('myUniqueId2'));
    }

    public function testRowStyle()
    {
        $grid = new Datagrid();
        $this->assertFalse($grid->hasRowStyles());

        $grid->addRowStyle($this->getMock('ZfcDatagrid\Column\Style\Bold'));
        $this->assertCount(1, $grid->getRowStyles());
        $this->assertTrue($grid->hasRowStyles());

        $grid->addRowStyle($this->getMock('ZfcDatagrid\Column\Style\Italic'));
        $this->assertCount(2, $grid->getRowStyles());
        $this->assertTrue($grid->hasRowStyles());
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

    public function testSetRendererDeprecated()
    {
        $grid = new Datagrid();

        ErrorHandler::start(E_USER_DEPRECATED);
        $grid->setRenderer('myRenderer');
        $err = ErrorHandler::stop();

        $this->assertInstanceOf('ErrorException', $err);
    }

    public function testRendererName()
    {
        // Default on HTTP
        $this->assertEquals('bootstrapTable', $this->grid->getRendererName());

        // Default on CLI
        $_SERVER['argv'] = array(
            'foo.php',
            'foo' => 'baz',
            'bar',
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

    public function testToolbarTemplate()
    {
        $grid = new Datagrid();

        $this->assertNull($grid->getToolbarTemplate());

        $grid->setToolbarTemplate('my-module/my-controller/grid-toolbar');
        $this->assertEquals('my-module/my-controller/grid-toolbar', $grid->getToolbarTemplate());
    }

    public function testViewModelDefault()
    {
        $grid = new Datagrid();

        $defaultView = $grid->getViewModel();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $defaultView);
        $this->assertSame($defaultView, $grid->getViewModel());
    }

    public function testSetViewModel()
    {
        $grid = new Datagrid();

        $customView = $this->getMock('Zend\View\Model\ViewModel');
        $grid->setViewModel($customView);
        $this->assertSame($customView, $grid->getViewModel());
    }

    public function testSetViewModelException()
    {
        $grid = new Datagrid();
        $grid->getViewModel();

        $customView = $this->getMock('Zend\View\Model\ViewModel');

        $this->setExpectedException('Exception', 'A viewModel is already set. Did you already called $grid->render() or $grid->getViewModel() before?');
        $grid->setViewModel($customView);
    }
}
