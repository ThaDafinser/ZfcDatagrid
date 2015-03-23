<?php
namespace ZfcDatagridTest\Renderer\JqGrid;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\JqGrid;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\JqGrid\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{
    private $options = array(
        'renderer' => array(
            'jqGrid' => array(
                'parameterNames' => array(
                    'sortColumns' => 'cols',
                    'sortDirections' => 'dirs',
                    'currentPage' => 'page',
                    'itemsPerPage' => 'items',
                ),
            ),
        ),
    );

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

    public function testGetRequestException()
    {
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);

        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $renderer = new JqGrid\Renderer();
        $renderer->setMvcEvent($mvcEvent);

        $this->setExpectedException('Exception', 'Request must be an instance of Zend\Http\PhpEnvironment\Request for HTML rendering');
        $renderer->getRequest();
    }

    public function testGetRequest()
    {
        $request = $this->getMock('Zend\Http\PhpEnvironment\Request', array(), array(), '', false);

        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
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
