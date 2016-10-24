<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use ZfcDatagrid\Column;
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\DataSource\PhpArray;

/**
 * @group Datagrid
 * @covers \ZfcDatagrid\Datagrid
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

        $cacheOptions                          = new \Zend\Cache\Storage\Adapter\MemoryOptions();
        $config['cache']['adapter']['name']    = 'Memory';
        $config['cache']['adapter']['options'] = $cacheOptions->toArray();

        $this->config = $config;

        $mvcEvent = $this->getMockBuilder(MvcEvent::class)->getMock();
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->getMockBuilder(Request::class)->getMock()));

        $this->grid = new Datagrid();
        $this->grid->setOptions($this->config);
        $this->grid->setMvcEvent($mvcEvent);
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
        $this->assertInstanceOf(\Zend\Session\Container::class, $this->grid->getSession());
        $this->assertEquals('defaultGrid', $this->grid->getSession()
            ->getName());

        $session = new Container('myName');

        $this->grid->setSession($session);
        $this->assertInstanceOf(\Zend\Session\Container::class, $this->grid->getSession());
        $this->assertSame($session, $this->grid->getSession());
        $this->assertEquals('myName', $this->grid->getSession()
            ->getName());
    }

    public function testCacheId()
    {
        $grid      = new Datagrid();
        $sessionId = $grid->getSession()
            ->getManager()
            ->getId();

        $this->assertEquals(md5($sessionId . '_defaultGrid'), $this->grid->getCacheId());

        $this->grid->setCacheId('myCacheId');
        $this->assertEquals('myCacheId', $this->grid->getCacheId());
    }

    public function testMvcEvent()
    {
        $this->assertInstanceOf(MvcEvent::class, $this->grid->getMvcEvent());

        $mvcEvent = $this->getMockBuilder(MvcEvent::class)->getMock();
        $this->grid->setMvcEvent($mvcEvent);
        $this->assertInstanceOf(MvcEvent::class, $this->grid->getMvcEvent());
        $this->assertEquals($mvcEvent, $this->grid->getMvcEvent());
    }

    public function testRequest()
    {
        $this->assertInstanceOf(Request::class, $this->grid->getRequest());
    }

    public function testTranslator()
    {
        $this->assertFalse($this->grid->hasTranslator());

        $this->grid->setTranslator($this->getMockBuilder(Translator::class)->getMock());

        $this->assertTrue($this->grid->hasTranslator());
        $this->assertInstanceOf(Translator::class, $this->grid->getTranslator());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDataSourceArray()
    {
        $grid = new Datagrid();
        $this->assertFalse($grid->hasDataSource());

        $grid->setDataSource([]);
        $this->assertTrue($grid->hasDataSource());
        $this->assertInstanceOf(PhpArray::class, $grid->getDataSource());

        $source = $this->getMockBuilder(PhpArray::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $grid->setDataSource($source);
        $this->assertTrue($grid->hasDataSource());

        $grid->setDataSource(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage For "Zend\Db\Sql\Select" also a "Zend\Db\Adapter\Sql" or "Zend\Db\Sql\Sql" is needed.
     */
    public function testDataSourceZendSelect()
    {
        $grid = new Datagrid();

        $this->assertFalse($grid->hasDataSource());

        $select = $this->getMockBuilder(\Zend\Db\Sql\Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $platform = $this->getMockBuilder(\Zend\Db\Adapter\Platform\Sqlite::class)
            ->getMock();
        $platform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('myPlatform'));

        $adapter = $this->getMockBuilder(\Zend\Db\Adapter\Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapter->expects($this->any())
            ->method('getPlatform')
            ->will($this->returnValue($platform));

        $grid->setDataSource($select, $adapter);
        $this->assertTrue($grid->hasDataSource());
        $this->assertInstanceOf(\ZfcDatagrid\Datasource\ZendSelect::class, $grid->getDataSource());
        $grid->setDataSource($select);
    }

    public function testDataSourceDoctrine()
    {
        $grid = new Datagrid();

        $this->assertFalse($grid->hasDataSource());

        $qb = $this->getMockBuilder(\Doctrine\ORM\QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $grid->setDataSource($qb);
        $this->assertTrue($grid->hasDataSource());
        $this->assertInstanceOf(\ZfcDatagrid\DataSource\Doctrine2::class, $grid->getDataSource());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage If providing a Collection, also the Doctrine\ORM\EntityManager is needed as a second parameter
     */
    public function testDataSourceDoctrineCollection()
    {
        $grid = new Datagrid();

        $this->assertFalse($grid->hasDataSource());

        $coll = $this->getMockBuilder(\Doctrine\Common\Collections\ArrayCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $em   = $this->getMockBuilder(\Doctrine\ORM\EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $grid->setDataSource($coll, $em);
        $this->assertTrue($grid->hasDataSource());
        $this->assertInstanceOf(\ZfcDatagrid\DataSource\Doctrine2Collection::class, $grid->getDataSource());

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
        $this->assertEquals([], $this->grid->getParameters());

        $this->grid->addParameter('myPara', 'test');

        $this->assertEquals([
            'myPara' => 'test',
        ], $this->grid->getParameters());

        $this->grid->setParameters([
            'other' => 'blubb',
        ]);
        $this->assertEquals([
            'other' => 'blubb',
        ], $this->grid->getParameters());
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
        $this->assertEquals([], $this->grid->getExportRenderers());

        $this->grid->setExportRenderers([
            'tcpdf' => 'PDF',
        ]);

        $this->assertEquals([
            'tcpdf' => 'PDF',
        ], $this->grid->getExportRenderers());
    }

    public function testAddColumn()
    {
        $this->assertEquals([], $this->grid->getColumns());

        $col = $this->getMockBuilder(\ZfcDatagrid\Column\AbstractColumn::class)
            ->setMethods(['getUniqueId'])
            ->getMock();

        $col->expects($this->any())
            ->method('getUniqueId')
            ->will($this->returnValue('myUniqueId'));

        $this->grid->addColumn($col);

        $this->assertCount(1, $this->grid->getColumns());

        $this->assertEquals(null, $this->grid->getColumnByUniqueId('notAvailable'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage createColumn() supports only a config array or instanceof Column\AbstractColumn as a parameter
     */
    public function testAddColumnInvalidArgumentException()
    {
        $grid = new Datagrid();

        $grid->addColumn(null);
    }

    public function testAddColumnArrayFQN()
    {
        $grid = new Datagrid();
        $this->assertEquals([], $grid->getColumns());

        $column = [
            'colType' => \ZfcDatagrid\Column\Select::class,
            'label'   => 'My label',
            'select'  => [
                'column' => 'myCol',
                'table'  => 'myTable',
            ],
        ];
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf(\ZfcDatagrid\Column\Select::class, $col);
        $this->assertEquals('My label', $col->getLabel());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column type: "ZfcDatagrid\Column\Unknown" not found!
     */
    public function testAddColumnArrayInvalidColType()
    {
        $grid = new Datagrid();
        $this->assertEquals([], $grid->getColumns());

        $column = [
            'colType' => 'ZfcDatagrid\Column\Unknown',
            'label'   => 'My label',
        ];

        $grid->addColumn($column);
    }

    public function testAddColumnArraySelect()
    {
        $grid = new Datagrid();
        $this->assertEquals([], $grid->getColumns());

        $column = [
            'label'  => 'My label',
            'select' => [
                'column' => 'myCol',
                'table'  => 'myTable',
            ],
        ];
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf(\ZfcDatagrid\Column\Select::class, $col);
        $this->assertEquals('My label', $col->getLabel());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage For "ZfcDatagrid\Column\Select" the option select[column] must be defined!
     */
    public function testAddColumnArraySelectInvalidArgumentException()
    {
        $grid = new Datagrid();
        $this->assertEquals([], $grid->getColumns());

        $column = [
            'label' => 'My label',
        ];

        $grid->addColumn($column);
    }

    public function testAddColumnArrayTypeAction()
    {
        $grid = new Datagrid();

        $column = [
            'colType' => 'action',
            'label'   => 'My action',
        ];
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('action');
        $this->assertInstanceOf(\ZfcDatagrid\Column\Action::class, $col);
        $this->assertEquals('My action', $col->getLabel());
    }

    public function testAddColumnArrayStyle()
    {
        $grid = new Datagrid();

        $bold = new Column\Style\Bold();

        $column = [
            'select' => [
                'column' => 'myCol',
                'table'  => 'myTable',
            ],
            'styles' => [
                $bold,
            ],
        ];
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf(\ZfcDatagrid\Column\Select::class, $col);

        $this->assertEquals([
            $bold,
        ], $col->getStyles());
    }

    public function testAddColumnArraySortDefaultMinimal()
    {
        $grid = new Datagrid();

        $column = [
            'select' => [
                'column' => 'myCol',
                'table'  => 'myTable',
            ],
            'sortDefault' => 1,
        ];
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf(\ZfcDatagrid\Column\Select::class, $col);

        $this->assertEquals([
            'priority'      => 1,
            'sortDirection' => 'ASC',
        ], $col->getSortDefault());
    }

    public function testAddColumnArraySortDefault()
    {
        $grid = new Datagrid();

        $column = [
            'select' => [
                'column' => 'myCol',
                'table'  => 'myTable',
            ],
            'sortDefault' => [
                1,
                'ASC',
            ],
        ];
        $grid->addColumn($column);

        $this->assertCount(1, $grid->getColumns());

        $col = $grid->getColumnByUniqueId('myTable_myCol');
        $this->assertInstanceOf(\ZfcDatagrid\Column\Select::class, $col);

        $this->assertEquals([
            'priority'      => 1,
            'sortDirection' => 'ASC',
        ], $col->getSortDefault());
    }

    public function testSetColumn()
    {
        $grid = new Datagrid();

        $this->assertEquals([], $grid->getColumns());

        $col = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col->setUniqueId('myUniqueId');

        $col2 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col2->setUniqueId('myUniqueId2');

        $grid->setColumns([
            $col,
            $col2,
        ]);

        $this->assertCount(2, $grid->getColumns());
        $this->assertEquals($col, $grid->getColumnByUniqueId('myUniqueId'));
        $this->assertEquals($col2, $grid->getColumnByUniqueId('myUniqueId2'));
    }

    public function testRowStyle()
    {
        $grid = new Datagrid();
        $this->assertFalse($grid->hasRowStyles());

        $grid->addRowStyle($this->getMockBuilder(\ZfcDatagrid\Column\Style\Bold::class)->getMock());
        $this->assertCount(1, $grid->getRowStyles());
        $this->assertTrue($grid->hasRowStyles());

        $grid->addRowStyle($this->getMockBuilder(\ZfcDatagrid\Column\Style\Italic::class)->getMock());
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

        $action = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Action\AbstractAction::class);
        $this->grid->setRowClickAction($action);
        $this->assertEquals($action, $this->grid->getRowClickAction());
        $this->assertTrue($this->grid->hasRowClickAction());
    }

    public function testRendererName()
    {
        // Default on HTTP
        $this->assertEquals('bootstrapTable', $this->grid->getRendererName());

        // Default on CLI
        $_SERVER['argv'] = [
            'foo.php',
            'foo' => 'baz',
            'bar',
        ];
        $_ENV["FOO_VAR"] = "bar";

        $request  = new \Zend\Console\Request();
        $mvcEvent = $this->getMockBuilder(\Zend\Mvc\MvcEvent::class)->getMock();
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
        $request              = new \Zend\Http\PhpEnvironment\Request();
        $mvcEvent             = $this->getMockBuilder(\Zend\Mvc\MvcEvent::class)->getMock();
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
        $this->assertInstanceOf(\Zend\View\Model\ViewModel::class, $defaultView);
        $this->assertSame($defaultView, $grid->getViewModel());
    }

    public function testSetViewModel()
    {
        $grid = new Datagrid();

        $customView = $this->getMockBuilder(\Zend\View\Model\ViewModel::class)->getMock();
        $grid->setViewModel($customView);
        $this->assertSame($customView, $grid->getViewModel());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage A viewModel is already set. Did you already called $grid->render() or $grid->getViewModel() before?
     */
    public function testSetViewModelException()
    {
        $grid = new Datagrid();
        $grid->getViewModel();

        $customView = $this->getMockBuilder(\Zend\View\Model\ViewModel::class)->getMock();

        $grid->setViewModel($customView);
    }
}
