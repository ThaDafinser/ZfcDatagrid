<?php
namespace ZfcDatagridTest\Renderer;

use ZfcDatagrid\Column\DataPopulation;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use PHPUnit_Framework_TestCase;
use Zend\Paginator;
use ZfcDatagridTest\DatagridMocks;
use ReflectionClass;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\AbstractRenderer
 */
class AbstractRendererTest extends PHPUnit_Framework_TestCase
{

    public function testOptions()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->setOptions(array(
            'test'
        ));
        
        $this->assertEquals(array(
            'test'
        ), $renderer->getOptions());
    }

    public function testRendererOptions()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));
        
        $this->assertEquals(array(), $renderer->getOptionsRenderer());
        
        $renderer->setOptions(array(
            'renderer' => array(
                'abstract' => array(
                    'test'
                )
            )
        ));
        
        $this->assertEquals(array(
            'test'
        ), $renderer->getOptionsRenderer());
    }

    public function testViewModel()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertNull($renderer->getViewModel());
        
        $viewModel = $this->getMock('Zend\View\Model\ViewModel');
        $renderer->setViewModel($viewModel);
        $this->assertSame($viewModel, $renderer->getViewModel());
    }

    public function testTemplate()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
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
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));
        
        $renderer->setOptions(array(
            'renderer' => array(
                'abstract' => array(
                    'templates' => array(
                        'layout' => 'config/my/template',
                        'toolbar' => 'config/my/toolbar'
                    )
                )
            )
        ));
        
        $this->assertEquals('config/my/template', $renderer->getTemplate());
        $this->assertEquals('config/my/toolbar', $renderer->getToolbarTemplate());
    }

    public function testPaginator()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertNull($renderer->getPaginator());
        
        $paginator = DatagridMocks::getPaginator();
        $renderer->setPaginator($paginator);
        
        $this->assertSame($paginator, $renderer->getPaginator());
    }

    public function testColumns()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertEquals(array(), $renderer->getColumns());
        
        $col = DatagridMocks::getColBasic();
        $renderer->setColumns(array(
            $col
        ));
        
        $this->assertEquals(array(
            $col
        ), $renderer->getColumns());
    }

    public function testRowStyles()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertEquals(array(), $renderer->getRowStyles());
        
        $bold = new Style\Bold();
        $renderer->setRowStyles(array(
            $bold
        ));
        $this->assertEquals(array(
            $bold
        ), $renderer->getRowStyles());
    }

    public function testCalculateColumnWidthPercent()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $reflection = new ReflectionClass(get_class($renderer));
        $method = $reflection->getMethod('calculateColumnWidthPercent');
        $method->setAccessible(true);
        
        $col1 = DatagridMocks::getColBasic();
        $cols = array(
            $col1
        );
        
        /*
         * Width lower than 100%
         */
        $this->assertEquals(5, $col1->getWidth());
        $method->invokeArgs($renderer, array(
            $cols
        ));
        $this->assertEquals(100, $col1->getWidth());
        
        /*
         * Width higher than 100%
         */
        $col1 = DatagridMocks::getColBasic();
        $col1->setWidth(90);
        
        $col2 = DatagridMocks::getColBasic();
        $col2->setWidth(60);
        $cols = array(
            $col1,
            $col2
        );
        
        $method->invokeArgs($renderer, array(
            $cols
        ));
        $this->assertEquals(60, $col1->getWidth());
        $this->assertEquals(40, $col2->getWidth());
    }

    public function testPaperWidth()
    {
        
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));
        
        $reflection = new ReflectionClass(get_class($renderer));
        $method = $reflection->getMethod('getPaperWidth');
        $method->setAccessible(true);
        
        /*
         * A4 landscape
         */
        $options = array(
            'renderer' => array(
                'abstract' => array(
                    'papersize' => 'A4',
                    'orientation' => 'landscape'
                )
            )
        );
        $renderer->setOptions($options);
        
        $width = $method->invoke($renderer);
        $this->assertEquals(297, $width);
        
        /*
         * A4 portrait
         */
        $options = array(
            'renderer' => array(
                'abstract' => array(
                    'papersize' => 'A4',
                    'orientation' => 'portrait'
                )
            )
        );
        $renderer->setOptions($options);
        
        $width = $method->invoke($renderer);
        $this->assertEquals(210, $width);
        
        /*
         * A0 portrait
         */
        $options = array(
            'renderer' => array(
                'abstract' => array(
                    'papersize' => 'A0',
                    'orientation' => 'portrait'
                )
            )
        );
        $renderer->setOptions($options);
        
        $width = $method->invoke($renderer);
        $this->assertEquals(841, $width);
        
        /*
         * A0 portrait
         */
        $options = array(
            'renderer' => array(
                'abstract' => array(
                    'papersize' => 'something',
                    'orientation' => 'portrait'
                )
            )
        );
        $renderer->setOptions($options);
        
        $this->setExpectedException('Exception', 'Currently only "A" paper formats are supported!');
        $width = $method->invoke($renderer);
    }

    public function testData()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertEquals(array(), $renderer->getData());
        
        $data = array(
            array(
                'myCol' => 123
            )
        );
        $renderer->setData($data);
        $this->assertEquals($data, $renderer->getData());
    }

    public function testCacheData()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertEquals(array(), $renderer->getCacheData());
        
        $data = array(
            'sortConditions' => '',
            'filters' => '',
            'currentPage' => 123,
            'data' => array(
                array(
                    'myCol' => 123
                )
            )
        );
        $renderer->setCacheData($data);
        $this->assertEquals($data, $renderer->getCacheData());
    }

    public function testMvcEvent()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $request = $this->getMock('Zend\Http\Request', array(), array(), '', false);
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
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
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $translator = $this->getMock('Zend\I18n\Translator\Translator', array(), array(), '', false);
        
        $this->assertNull($renderer->getTranslator());
        $renderer->setTranslator($translator);
        $this->assertSame($translator, $renderer->getTranslator());
    }

    public function testTitle()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertNull($renderer->getTitle());
        
        $renderer->setTitle('My title');
        $this->assertEquals('My title', $renderer->getTitle());
    }

    public function testCacheId()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertNull($renderer->getCacheId());
        
        $renderer->setCacheId('a_cache_id');
        $this->assertEquals('a_cache_id', $renderer->getCacheId());
    }

    public function testFilename()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $reflection = new ReflectionClass(get_class($renderer));
        $method = $reflection->getMethod('getFilename');
        $method->setAccessible(true);
        
        $filename = $method->invokeArgs($renderer, array());
        $this->assertEquals(date('Y-m-d_H-i-s'), $filename);
        
        $renderer->setTitle('My title');
        
        $filename = $method->invokeArgs($renderer, array());
        $this->assertEquals(date('Y-m-d_H-i-s') . '_My_title', $filename);
    }

    public function testGetSortConditionsSortEmpty()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        // no sorting
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals(array(), $sortConditions);
        
        // 2nd call -> from array
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals(array(), $sortConditions);
    }

    public function testGetSortConditionsSortDefault()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $col1 = DatagridMocks::getColBasic();
        $col1->setUniqueId('myCol');
        $col1->setSortDefault(1);
        $renderer->setColumns(array(
            $col1
        ));
        
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals(array(
            1 => array(
                'column' => $col1,
                'sortDirection' => 'ASC'
            )
        ), $sortConditions);
    }

    public function testCustomFiltered()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertFalse($renderer->isCustomFiltered());
        
        $renderer->setCustomFiltered(true);
        $this->assertTrue($renderer->isCustomFiltered());
        
        $renderer->setCustomFiltered(false);
        $this->assertFalse($renderer->isCustomFiltered());
    }

    public function testGetFiltersDefault()
    {
        $request = $this->getMock('Zend\Http\PhpEnvironment\Request', array(), array(), '', false);
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(false));
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->setMvcEvent($mvcEvent);
        
        $col1 = DatagridMocks::getColBasic();
        $col1->setUniqueId('myCol');
        $col1->setFilterDefaultValue('filterValue');
        $renderer->setColumns(array(
            $col1
        ));
        
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
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $col1 = DatagridMocks::getColBasic();
        $col1->setUniqueId('myCol');
        $col1->setFilterDefaultValue('filterValue');
        $renderer->setColumns(array(
            $col1
        ));
        
        $renderer->setCustomFiltered(true);
        $this->assertEquals(array(), $renderer->getFilters());
    }

    public function testCurrentPageNumber()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertEquals(1, $renderer->getCurrentPageNumber());
        
        $renderer->setCurrentPageNumber(25);
        $this->assertEquals(25, $renderer->getCurrentPageNumber());
    }

    public function testGetItemsPerPage()
    {
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertEquals(25, $renderer->getItemsPerPage());
        $this->assertEquals(100, $renderer->getItemsPerPage(100));
        
        // exports are unlimited
        /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
        $renderer = new \ZfcDatagrid\Renderer\TCPDF\Renderer();
        $this->assertEquals(- 1, $renderer->getItemsPerPage());
    }
}

