<?php
namespace ZfcDatagridTest\Renderer\BootstrapTable;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\BootstrapTable;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\BootstrapTable\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $renderer = new BootstrapTable\Renderer();

        $this->assertEquals('bootstrapTable', $renderer->getName());
    }

    public function testIsExport()
    {
        $renderer = new BootstrapTable\Renderer();

        $this->assertFalse($renderer->isExport());
    }

    public function testIsHtml()
    {
        $renderer = new BootstrapTable\Renderer();

        $this->assertTrue($renderer->isHtml());
    }

    public function testGetRequestException()
    {
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);

        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
        $mvcEvent->expects($this->any())
        ->method('getRequest')
        ->will($this->returnValue($request));

        $renderer = new BootstrapTable\Renderer();
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

        $renderer = new BootstrapTable\Renderer();
        $renderer->setMvcEvent($mvcEvent);

        $this->assertEquals($request, $renderer->getRequest());
    }
}
