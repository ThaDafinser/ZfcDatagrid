<?php
namespace ZfcDatagridTest\Renderer\BootstrapTable\View\Helper;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\JqGrid\View\Helper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Style\AbstractColor;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\JqGrid\View\Helper\Columns
 */
class ColumnsTest extends PHPUnit_Framework_TestCase
{

    private $sm;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $myCol;

    public function setUp()
    {
        $sm2 = $this->getMock('Zend\ServiceManager\ServiceManager');
        
        $sm = $this->getMock('Zend\View\HelperPluginManager', array(), array(), '', false);
        $sm->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($sm2));
        $this->sm = $sm;
        
        $myCol = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $myCol->setUniqueId('myCol');
        
        $this->myCol = $myCol;
    }

    public function testServiceLocator()
    {
        $helper = new Helper\Columns();
        
        $helper->setServiceLocator($this->sm);
        $this->assertSame($this->sm, $helper->getServiceLocator());
    }

    public function testSimple()
    {
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);
        
        $cols = array(
            $this->myCol
        );
        
        $result = $helper($cols);
        
        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('search: true}]', $result);
    }
}
