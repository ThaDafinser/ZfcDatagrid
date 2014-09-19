<?php
namespace ZfcDatagridTest\Renderer\BootstrapTable\View\Helper;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Renderer\BootstrapTable\View\Helper\TableRow;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Style\AbstractColor;

/**
 * @group Renderer
 * @covers ZfcDatagrid\Renderer\BootstrapTable\View\Helper\TableRow
 */
class TableRowTest extends PHPUnit_Framework_TestCase
{
    private $rowWithoutId = array(
        'myCol' => 'First value',
    );

    private $rowWithId = array(
        'idConcated' => 1,
        'myCol' => 'First value',
    );

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $myCol;

    public function setUp()
    {
        $myCol = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $myCol->setUniqueId('myCol');

        $this->myCol = $myCol;

        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceManager');
    }

    public function testCanExecute()
    {
        $helper = new TableRow();

        $myCol = clone $this->myCol;

        $cols = array(
            $myCol,
        );

        // without id
        $html = $helper($this->rowWithoutId, $cols);

        $this->assertStringStartsWith('<tr>', $html);
        $this->assertStringEndsWith('</tr>', $html);

        // with id
        $html = $helper($this->rowWithId, $cols);

        $this->assertStringStartsWith('<tr id="1">', $html);
        $this->assertStringEndsWith('</tr>', $html);
    }

    public function testHidden()
    {
        $helper = new TableRow();

        $myCol = clone $this->myCol;
        $myCol->setHidden(true);

        $cols = array(
            $myCol,
        );

        $html = $helper($this->rowWithId, $cols);

        $this->assertContains('<td class="hidden"', $html);
    }

    public function testType()
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $helper = new TableRow();

        $myCol = clone $this->myCol;
        $myCol->setType(new Type\Number());

        $cols = array(
            $myCol,
        );

        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="text-align: right"', $html);

        $myCol->setType(new Type\PhpArray());
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<pre>First value</pre>', $html);
    }

    public function testStyle()
    {
        $helper = new TableRow();

        // bold
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\Bold());

        $cols = array(
            $myCol,
        );

        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="font-weight: bold"', $html);

        // italic
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\Italic());

        $cols = array(
            $myCol,
        );
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="font-style: italic"', $html);

        // color
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\Color(AbstractColor::$RED));

        $cols = array(
            $myCol,
        );
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="color: #ff0000"', $html);

        // background color
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\BackgroundColor(AbstractColor::$GREEN));

        $cols = array(
            $myCol,
        );
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="background-color: #00ff00"', $html);

        // exception
        $style = $this->getMockForAbstractClass('ZfcDatagrid\Column\Style\AbstractStyle');

        $myCol = clone $this->myCol;
        $myCol->addStyle($style);

        $cols = array(
            $myCol,
        );

        $this->setExpectedException('InvalidArgumentException');
        $html = $helper($this->rowWithId, $cols);
    }

    public function testAction()
    {
        $rowData = $this->rowWithId;
        $rowData['action'] = '';

        $helper = new TableRow();
        $helper->setServiceLocator($this->serviceLocator);

        // must be instanceof Column\Select...
        $myCol = new Column\Select('myCol');

        $action = new Column\Action\Checkbox();
        $action->setLink('http://example.com');

        $colAction = new Column\Action();
        $colAction->addAction($action);

        $cols = array(
            $myCol,
            $colAction,
        );

        $html = $helper($rowData, $cols);
        $this->assertContains('<input type="checkbox"', $html);

        // row action
        $cols = array(
            $myCol,
            $colAction,
        );
        $html = $helper($rowData, $cols, $action);
        $this->assertContains('<a href="http://example.com', $html);
    }
}
