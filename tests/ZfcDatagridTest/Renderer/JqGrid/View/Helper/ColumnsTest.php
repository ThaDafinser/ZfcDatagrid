<?php
namespace ZfcDatagridTest\Renderer\JqGrid\View\Helper;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\JqGrid\View\Helper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Filter;

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
            clone $this->myCol,
        );

        $result = $helper($cols);

        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('"clearSearch":false}}]', $result);
    }

    public function testStyleBold()
    {
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);

        $col1 = clone $this->myCol;
        $col1->addStyle(new Style\Bold());
        $cols = array(
            $col1,
        );

        $result = $helper($cols);

        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('<span style="font-weight: bold;">\' + cellvalue + \'</span>\'; return cellvalue; },searchoptions: {"clearSearch":false}}]', $result);
    }

    public function testStyleItalic()
    {
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);

        $col1 = clone $this->myCol;
        $col1->addStyle(new Style\Italic());
        $cols = array(
            $col1,
        );

        $result = $helper($cols);

        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('<span style="font-style: italic;">\' + cellvalue + \'</span>\'; return cellvalue; },searchoptions: {"clearSearch":false}}]', $result);
    }

    public function testStyleColor()
    {
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);

        $col1 = clone $this->myCol;
        $col1->addStyle(new Style\Color(Style\Color::$RED));
        $cols = array(
            $col1,
        );

        $result = $helper($cols);

        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('<span style="color: #ff0000;">\' + cellvalue + \'</span>\'; return cellvalue; },searchoptions: {"clearSearch":false}}]', $result);
    }

    public function testStyleBackgroundColor()
    {
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);

        $col1 = clone $this->myCol;
        $col1->addStyle(new Style\BackgroundColor(Style\BackgroundColor::$RED));
        $cols = array(
            $col1,
        );

        $result = $helper($cols);

        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('search: true,searchoptions: {"clearSearch":false}}]', $result);
    }

    public function testStyleException()
    {
        $styleMock = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);

        $col1 = clone $this->myCol;
        $col1->addStyle($styleMock);
        $cols = array(
            $col1,
        );

        $this->setExpectedException('Exception', 'Not defined style: "'.get_class($styleMock).'"');
        $result = $helper($cols);

        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('search: true}]', $result);
    }

    public function testStyleByValueEqual()
    {
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);

        $col1 = clone $this->myCol;

        $style = new Style\Bold();
        $style->addByValue($col1, 123);

        $col1->addStyle($style);
        $cols = array(
            $col1,
        );

        $result = $helper($cols);

        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('if (rowObject.myCol == \'123\') {cellvalue = \'<span style="font-weight: bold;">\' + cellvalue + \'</span>\';} return cellvalue; },searchoptions: {"clearSearch":false}}]', $result);
    }

    public function testStyleByValueNotEqual()
    {
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);

        $col1 = clone $this->myCol;

        $style = new Style\Bold();
        $style->addByValue($col1, 123, Filter::NOT_EQUAL);

        $col1->addStyle($style);
        $cols = array(
            $col1,
        );

        $result = $helper($cols);

        $this->assertStringStartsWith('[{name:', $result);
        $this->assertStringEndsWith('if (rowObject.myCol != \'123\') {cellvalue = \'<span style="font-weight: bold;">\' + cellvalue + \'</span>\';} return cellvalue; },searchoptions: {"clearSearch":false}}]', $result);
    }

    public function testStyleByValueNotSupported()
    {
        $helper = new Helper\Columns();
        $helper->setServiceLocator($this->sm);

        $col1 = clone $this->myCol;

        $style = new Style\Bold();
        $style->addByValue($col1, 123, Filter::IN);

        $col1->addStyle($style);
        $cols = array(
            $col1,
        );

        $this->setExpectedException('Exception', 'Currently not supported filter operation: "'.Filter::IN.'"');
        $result = $helper($cols);
    }
}
