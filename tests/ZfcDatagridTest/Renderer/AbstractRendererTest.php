<?php
namespace ZfcDatagridTest\Renderer;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ZfcDatagrid\Column\Style;

/**
 * @group Renderer
 * @covers \ZfcDatagrid\Renderer\AbstractRenderer
 */
class AbstractRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $colMock;

    public function setUp()
    {
        $this->colMock = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
    }

    public function testOptions()
    {
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);
        $renderer->setOptions([
            'test',
        ]);

        $this->assertEquals([
            'test',
        ], $renderer->getOptions());
    }

    public function testRendererOptions()
    {
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));

        $this->assertEquals([], $renderer->getOptionsRenderer());

        $renderer->setOptions([
            'renderer' => [
                'abstract' => [
                    'test',
                ],
            ],
        ]);

        $this->assertEquals([
            'test',
        ], $renderer->getOptionsRenderer());
    }

    public function testViewModel()
    {
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertNull($renderer->getViewModel());

        $viewModel = $this->getMockBuilder(\Zend\View\Model\ViewModel::class)
            ->getMock();
        $renderer->setViewModel($viewModel);
        $this->assertSame($viewModel, $renderer->getViewModel());
    }

    public function testTemplate()
    {
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));

        $this->assertEquals('zfc-datagrid/renderer/abstract/layout', $renderer->getTemplate());
        $this->assertEquals('zfc-datagrid/toolbar/toolbar', $renderer->getToolbarTemplate());

        $renderer->setTemplate('blubb/layout');
        $this->assertEquals('blubb/layout', $renderer->getTemplate());

        $renderer->setToolbarTemplate('blubb/toolbar');
        $this->assertEquals('blubb/toolbar', $renderer->getToolbarTemplate());
    }

    public function testTemplateConfig()
    {
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));

        $renderer->setOptions([
            'renderer' => [
                'abstract' => [
                    'templates' => [
                        'layout'  => 'config/my/template',
                        'toolbar' => 'config/my/toolbar',
                    ],
                ],
            ],
        ]);

        $this->assertEquals('config/my/template', $renderer->getTemplate());
        $this->assertEquals('config/my/toolbar', $renderer->getToolbarTemplate());
    }

    public function testPaginator()
    {
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertNull($renderer->getPaginator());

        $testCollection = range(1, 101);
        $pagintorMock   = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($testCollection));
        $renderer->setPaginator($pagintorMock);

        $this->assertSame($pagintorMock, $renderer->getPaginator());
    }

    public function testColumns()
    {
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertEquals([], $renderer->getColumns());

        $col = clone $this->colMock;
        $renderer->setColumns([
            $col,
        ]);

        $this->assertEquals([
            $col,
        ], $renderer->getColumns());
    }

    public function testRowStyles()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertEquals([], $renderer->getRowStyles());

        $bold = new Style\Bold();
        $renderer->setRowStyles([
            $bold,
        ]);
        $this->assertEquals([
            $bold,
        ], $renderer->getRowStyles());
    }

    public function testCalculateColumnWidthPercent()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $reflection = new ReflectionClass(get_class($renderer));
        $method     = $reflection->getMethod('calculateColumnWidthPercent');
        $method->setAccessible(true);

        $col1 = clone $this->colMock;
        $cols = [
            $col1,
        ];

        /*
         * Width lower than 100%
         */
        $this->assertEquals(5, $col1->getWidth());
        $method->invokeArgs($renderer, [
            $cols,
        ]);
        $this->assertEquals(100, $col1->getWidth());

        /*
         * Width higher than 100%
         */
        $col1 = clone $this->colMock;
        $col1->setWidth(90);

        $col2 = clone $this->colMock;
        $col2->setWidth(60);
        $cols = [
            $col1,
            $col2,
        ];

        $method->invokeArgs($renderer, [
            $cols,
        ]);
        $this->assertEquals(60, $col1->getWidth());
        $this->assertEquals(40, $col2->getWidth());
    }

    public function testData()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertEquals([], $renderer->getData());

        $data = [
            [
                'myCol' => 123,
            ],
        ];
        $renderer->setData($data);
        $this->assertEquals($data, $renderer->getData());
    }

//     public function testCacheData()
//     {
//         $cache = $this->getMockForAbstractClass(\Zend\Cache\Storage\Adapter\AbstractAdapter::class);

//         /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
//         $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);
//         $renderer->setCache($cache);

//         $this->assertEquals(array(), $renderer->getCacheData());

//         $data = array(
//             'sortConditions' => '',
//             'filters' => '',
//             'currentPage' => 123,
//             'data' => array(
//                 array(
//                     'myCol' => 123
//                 )
//             )
//         );
//         $renderer->setCacheData($data);
//         $this->assertEquals($data, $renderer->getCacheData());
//     }

    public function testMvcEvent()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $request = $this->getMockBuilder(\Zend\Http\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mvcEvent = $this->getMockBuilder(\Zend\Mvc\MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->assertNull($renderer->getMvcEvent());
        $renderer->setMvcEvent($mvcEvent);
        $this->assertSame($mvcEvent, $renderer->getMvcEvent());

        // request
        $this->assertSame($request, $renderer->getRequest());
    }

    public function testTranslator()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $translator = $this->getMockBuilder(\Zend\I18n\Translator\Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertNull($renderer->getTranslator());
        $renderer->setTranslator($translator);
        $this->assertSame($translator, $renderer->getTranslator());
    }

    public function testTranslate()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);
        $this->assertEquals('foobar', $renderer->translate('foobar'));

        $translator = $this->getMockBuilder(\Zend\I18n\Translator\Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['translate'])
            ->getMock();
        $translator->expects($this->any())
            ->method('translate')
            ->willReturn('barfoo');

        $renderer->setTranslator($translator);

        $this->assertEquals('barfoo', $renderer->translate('foobar'));
    }

    public function testTitle()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertNull($renderer->getTitle());

        $renderer->setTitle('My title');
        $this->assertEquals('My title', $renderer->getTitle());
    }

    public function testCacheId()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertNull($renderer->getCacheId());

        $renderer->setCacheId('a_cache_id');
        $this->assertEquals('a_cache_id', $renderer->getCacheId());
    }

    public function testGetSortConditionsSortEmpty()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        // no sorting
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals([], $sortConditions);

        // 2nd call -> from array
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals([], $sortConditions);
    }

    public function testGetSortConditionsSortDefault()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $col1 = clone $this->colMock;
        $col1->setUniqueId('myCol');
        $col1->setSortDefault(1);
        $renderer->setColumns([
            $col1,
        ]);

        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals([
            1 => [
                'column'        => $col1,
                'sortDirection' => 'ASC',
            ],
        ], $sortConditions);
    }

    public function testGetFiltersDefault()
    {
        $request = $this->getMockBuilder(\Zend\Http\PhpEnvironment\Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(false));

        $mvcEvent = $this->getMockBuilder(\Zend\Mvc\MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);
        $renderer->setMvcEvent($mvcEvent);

        $col1 = clone $this->colMock;
        $col1->setUniqueId('myCol');
        $col1->setFilterDefaultValue('filterValue');
        $renderer->setColumns([
            $col1,
        ]);

        $filters = $renderer->getFiltersDefault();
        $this->assertCount(1, $filters);
        $this->assertInstanceOf('ZfcDatagrid\Filter', $filters[0]);

        // getFilters are the same like getFiltersDefault in this case
        $this->assertEquals($filters, $renderer->getFilters());

        // 2nd call from array cache
        $this->assertEquals($filters, $renderer->getFilters());
    }

    public function testGetFiltersNothingOnlyFromCustom()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $col1 = clone $this->colMock;
        $col1->setUniqueId('myCol');
        $col1->setFilterDefaultValue('filterValue');
        $renderer->setColumns([
            $col1,
        ]);
    }

    public function testCurrentPageNumber()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertEquals(1, $renderer->getCurrentPageNumber());

        $renderer->setCurrentPageNumber(25);
        $this->assertEquals(25, $renderer->getCurrentPageNumber());
    }

    public function testGetItemsPerPage()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass(\ZfcDatagrid\Renderer\AbstractRenderer::class);

        $this->assertEquals(25, $renderer->getItemsPerPage());
        $this->assertEquals(100, $renderer->getItemsPerPage(100));

        // exports are unlimited
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = new \ZfcDatagrid\Renderer\TCPDF\Renderer();
        $this->assertEquals(- 1, $renderer->getItemsPerPage());
    }
}
