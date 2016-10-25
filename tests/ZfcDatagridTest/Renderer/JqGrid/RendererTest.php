<?php
namespace ZfcDatagridTest\Renderer\JqGrid;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\JqGrid;

/**
 * @group Renderer
 * @covers \ZfcDatagrid\Renderer\JqGrid\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{
    private $options = [
        'renderer' => [
            'jqGrid' => [
                'parameterNames' => [
                    'sortColumns'    => 'cols',
                    'sortDirections' => 'dirs',
                    'currentPage'    => 'page',
                    'itemsPerPage'   => 'items',
                ],
            ],
        ],
    ];

    public function testGetName()
    {
        $renderer = new JqGrid\Renderer();

        $this->assertEquals('jqGrid', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new JqGrid\Renderer();

        $this->assertFalse($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new JqGrid\Renderer();

        $this->assertTrue($renderer->isHtml());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Request must be an instance of Zend\Http\PhpEnvironment\Request for HTML rendering
     */
    public function testGetRequestException()
    {
        $request = $this->getMockBuilder(\Zend\Console\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mvcEvent = $this->getMockBuilder(\Zend\Mvc\MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new JqGrid\Renderer();
        $renderer->setMvcEvent($mvcEvent);

        $renderer->getRequest();
    }

    public function testGetRequest()
    {
        $request = $this->getMockBuilder(\Zend\Http\PhpEnvironment\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mvcEvent = $this->getMockBuilder(\Zend\Mvc\MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new JqGrid\Renderer();
        $renderer->setMvcEvent($mvcEvent);

        $this->assertEquals($request, $renderer->getRequest());
    }

    public function testGetSortConditions()
    {
        $renderer = new JqGrid\Renderer();
    }
}
