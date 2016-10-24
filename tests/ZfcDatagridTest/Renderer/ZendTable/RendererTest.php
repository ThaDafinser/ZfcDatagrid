<?php
namespace ZfcDatagridTest\Renderer\ZendTable;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ZfcDatagrid\Renderer\ZendTable;

/**
 * @group Renderer
 * @covers \ZfcDatagrid\Renderer\ZendTable\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{
    private $options = [
        'renderer' => [
            'zendTable' => [
                'parameterNames' => [
                    'sortColumns'    => 'cols',
                    'sortDirections' => 'dirs',
                    'currentPage'    => 'page',
                    'itemsPerPage'   => 'items',
                ],
            ],
        ],
    ];

    private $consoleWidth = 77;

    /**
     *
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $requestMock;

    /**
     *
     * @var \Zend\Mvc\MvcEvent
     */
    private $mvcEventMock;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $colMock;

    public function setUp()
    {
        $this->requestMock  = $this->getMockBuilder(\Zend\Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mvcEventMock = $this->getMockBuilder(\Zend\Mvc\MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->colMock = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
    }

    public function testGetName()
    {
        $renderer = new ZendTable\Renderer();

        $this->assertEquals('zendTable', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new ZendTable\Renderer();

        $this->assertFalse($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new ZendTable\Renderer();

        $this->assertFalse($renderer->isHtml());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Request must be an instance of Zend\Console\Request for console rendering
     */
    public function testGetRequestException()
    {
        $request = $this->getMockBuilder(\Zend\Http\PhpEnvironment\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setMvcEvent($mvcEvent);

        $renderer->getRequest();
    }

    public function testGetRequest()
    {
        $request = clone $this->requestMock;

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setMvcEvent($mvcEvent);

        $this->assertEquals($request, $renderer->getRequest());
    }

    public function testConsoleAdapter()
    {
        $renderer = new ZendTable\Renderer();

        $this->assertInstanceOf(\Zend\Console\Adapter\AdapterInterface::class, $renderer->getConsoleAdapter());

        $adapter = $this->getMockForAbstractClass(\Zend\Console\Adapter\AbstractAdapter::class);

        $this->assertNotSame($adapter, $renderer->getConsoleAdapter());
        $renderer->setConsoleAdapter($adapter);
        $this->assertSame($adapter, $renderer->getConsoleAdapter());
    }

    public function testGetSortConditionsDefaultEmpty()
    {
        $request = clone $this->requestMock;

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);

        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals([], $sortConditions);

        // 2nd call from array cache
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals([], $sortConditions);
    }

    public function testGetSortConditionsFromRequest()
    {
        $request = clone $this->requestMock;

        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnCallback(function ($name) {
            if ('dirs' == $name) {
                return 'ASC,DESC';
            } else {
                return 'myCol1,myCol2';
            }
        }));

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);

        $col1 = clone $this->colMock;
        $col1->setUniqueId('myCol1');

        $col2 = clone $this->colMock;
        $col2->setUniqueId('myCol2');

        $renderer->setColumns([
            $col1,
            $col2,
        ]);

        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals([
            [
                'sortDirection' => 'ASC',
                'column'        => $col1,
            ],
            [
                'sortDirection' => 'DESC',
                'column'        => $col2,
            ],
        ], $sortConditions);
    }

    /**
     * One direction is not defined (ASC or desc allowed) and one is empty
     *
     * @return string
     */
    public function testGetSortConditionsFromRequestDefaultSortDirection()
    {
        $request = clone $this->requestMock;

        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnCallback(function ($name) {
            if ('dirs' == $name) {
                return 'WRONG_DIRECTION';
            } else {
                return 'myCol1,myCol2';
            }
        }));

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);

        $col1 = clone $this->colMock;
        $col1->setUniqueId('myCol1');

        $col2 = clone $this->colMock;
        $col2->setUniqueId('myCol2');

        $renderer->setColumns([
            $col1,
            $col2,
        ]);

        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals([
            [
                'sortDirection' => 'ASC',
                'column'        => $col1,
            ],
            [
                'sortDirection' => 'ASC',
                'column'        => $col2,
            ],
        ], $sortConditions);
    }

    public function testGetCurrentPageNumberDefault()
    {
        $request = clone $this->requestMock;

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);

        $this->assertEquals(1, $renderer->getCurrentPageNumber());
    }

    public function testGetCurrentPageNumberUser()
    {
        $request = clone $this->requestMock;
        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue(3));

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);

        $this->assertEquals(3, $renderer->getCurrentPageNumber());
    }

    public function testGetItemsPerPage()
    {
        $request = clone $this->requestMock;

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);

        $this->assertEquals(25, $renderer->getItemsPerPage());
    }

    public function testGetItemsPerPageUser()
    {
        $request = clone $this->requestMock;
        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue(99));

        $mvcEvent = clone $this->mvcEventMock;
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);

        $this->assertEquals(99, $renderer->getItemsPerPage());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No columns to display available
     */
    public function testGetColumnsToDisplay()
    {
        $reflection = new ReflectionClass(\ZfcDatagrid\Renderer\ZendTable\Renderer::class);
        $method     = $reflection->getMethod('getColumnsToDisplay');
        $method->setAccessible(true);

        $col1 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col1->setWidth(30);

        $col2 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col2->setWidth(20);

        $col3 = $this->getMockBuilder(\ZfcDatagrid\Column\Action::class)
            ->disableOriginalConstructor()
            ->getMock();
        $col3->setWidth(20);

        $renderer = new ZendTable\Renderer();
        $renderer->setColumns([
            $col1,
            $col2,
            $col3,
        ]);

        $result = $method->invoke($renderer);

        // $col3 is substracted, because its an action
        $this->assertSame([
            $col1,
            $col2,
        ], $result);

        // 2nd call from "cache"
        $result = $method->invoke($renderer);
        $this->assertSame([
            $col1,
            $col2,
        ], $result);

        $renderer = new ZendTable\Renderer();
        $method->invoke($renderer);
    }

    public function testGetColumnWidthsSmaller()
    {
        $reflection = new ReflectionClass(\ZfcDatagrid\Renderer\ZendTable\Renderer::class);
        $method     = $reflection->getMethod('getColumnWidths');
        $method->setAccessible(true);

        $col1 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col1->setWidth(30);

        $col2 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col2->setWidth(20);

        $consoleAdapter = $this->getMockForAbstractClass(\Zend\Console\Adapter\AbstractAdapter::class);
        $renderer       = new ZendTable\Renderer();
        $renderer->setConsoleAdapter($consoleAdapter);
        $renderer->setColumns([
            $col1,
            $col2,
        ]);

        $result = $method->invoke($renderer);
        $this->assertEquals($this->consoleWidth, array_sum($result));

        $this->assertEquals([
            47,
            30,
        ], $result);
    }

    public function testGetColumnWidthsLarger()
    {
        $reflection = new ReflectionClass(\ZfcDatagrid\Renderer\ZendTable\Renderer::class);
        $method     = $reflection->getMethod('getColumnWidths');
        $method->setAccessible(true);

        $col1 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col1->setWidth(60);

        $col2 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col2->setWidth(40);

        $consoleAdapter = $this->getMockForAbstractClass(\Zend\Console\Adapter\AbstractAdapter::class);
        $renderer       = new ZendTable\Renderer();
        $renderer->setConsoleAdapter($consoleAdapter);
        $renderer->setColumns([
            $col1,
            $col2,
        ]);

        $result = $method->invoke($renderer);
        $this->assertEquals($this->consoleWidth, array_sum($result));

        $this->assertEquals([
            47,
            30,
        ], $result);
    }

    public function testGetColumnWidthsRoundNecessary()
    {
        $reflection = new ReflectionClass(\ZfcDatagrid\Renderer\ZendTable\Renderer::class);
        $method     = $reflection->getMethod('getColumnWidths');
        $method->setAccessible(true);

        $col1 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col1->setWidth(72);

        $col2 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col2->setWidth(5);

        $consoleAdapter = $this->getMockForAbstractClass(\Zend\Console\Adapter\AbstractAdapter::class);
        $renderer       = new ZendTable\Renderer();
        $renderer->setConsoleAdapter($consoleAdapter);
        $renderer->setColumns([
            $col1,
            $col2,
        ]);

        $result = $method->invoke($renderer);
        $this->assertEquals($this->consoleWidth, array_sum($result));

        $this->assertEquals([
            72,
            5,
        ], $result);
    }
}
