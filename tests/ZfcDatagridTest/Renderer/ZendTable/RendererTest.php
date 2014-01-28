<?php
namespace ZfcDatagridTest\Renderer\ZendTable;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\ZendTable;
use ZfcDatagridTest\DatagridMocks;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\ZendTable\Renderer
 */
class RendererTest extends PHPUnit_Framework_TestCase
{

    private $options = array(
        'renderer' => array(
            'zendTable' => array(
                'parameterNames' => array(
                    'sortColumns' => 'cols',
                    'sortDirections' => 'dirs',
                    'currentPage' => 'page',
                    'itemsPerPage' => 'items'
                )
            )
        )
    );

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

    public function testGetRequestException()
    {
        $request = $this->getMock('Zend\Http\PhpEnvironment\Request', array(), array(), '', false);
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        
        $renderer = new ZendTable\Renderer();
        $renderer->setMvcEvent($mvcEvent);
        
        $this->setExpectedException('Exception', 'Request must be an instance of Zend\Console\Request for console rendering');
        $renderer->getRequest();
    }

    public function testGetRequest()
    {
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        
        $renderer = new ZendTable\Renderer();
        $renderer->setMvcEvent($mvcEvent);
        
        $this->assertEquals($request, $renderer->getRequest());
    }

    public function testGetSortConditionsDefaultEmpty()
    {
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        
        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);
        
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals(array(), $sortConditions);
        
        // 2nd call from array cache
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals(array(), $sortConditions);
    }

    public function testGetSortConditionsFromRequest()
    {
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);
        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnCallback(function ($name)
        {
            if ($name == 'dirs') {
                return 'ASC,DESC';
            } else {
                return 'myCol1,myCol2';
            }
        }));
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        
        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);
        
        $col1 = DatagridMocks::getColBasic();
        $col1->setUniqueId('myCol1');
        
        $col2 = DatagridMocks::getColBasic();
        $col2->setUniqueId('myCol2');
        
        $renderer->setColumns(array(
            $col1,
            $col2
        ));
        
        $sortConditions = $renderer->getSortConditions();
        $this->assertEquals(array(
            array(
                'sortDirection' => 'ASC',
                'column' => $col1
            ),
            array(
                'sortDirection' => 'DESC',
                'column' => $col2
            )
        ), $sortConditions);
    }

    public function testGetCurrentPageNumberDefault()
    {
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
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
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);
        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue(3));
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
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
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
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
        $request = $this->getMock('Zend\Console\Request', array(), array(), '', false);
        $request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue(99));
        
        $mvcEvent = $this->getMock('Zend\Mvc\MvcEvent', array(), array(), '', false);
        $mvcEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        
        $renderer = new ZendTable\Renderer();
        $renderer->setOptions($this->options);
        $renderer->setMvcEvent($mvcEvent);
        
        $this->assertEquals(99, $renderer->getItemsPerPage());
    }
}
