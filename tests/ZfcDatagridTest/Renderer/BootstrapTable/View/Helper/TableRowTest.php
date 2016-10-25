<?php
namespace ZfcDatagridTest\Renderer\BootstrapTable\View\Helper;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Style\AbstractColor;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Renderer\BootstrapTable\View\Helper\TableRow;

/**
 * @group Renderer
 * @covers \ZfcDatagrid\Renderer\BootstrapTable\View\Helper\TableRow
 */
class TableRowTest extends PHPUnit_Framework_TestCase
{
    private $rowWithoutId = [
        'myCol' => 'First value',
    ];

    private $rowWithId = [
        'idConcated' => 1,
        'myCol'      => 'First value',
    ];

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $myCol;

    public function setUp()
    {
        $myCol = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $myCol->setUniqueId('myCol');

        $this->myCol = $myCol;

        $this->serviceLocator = $this->getMockBuilder(\Zend\ServiceManager\ServiceManager::class)
            ->getMock();
    }

    public function testCanExecute()
    {
        $helper = new TableRow();

        $myCol = clone $this->myCol;

        $cols = [
            $myCol,
        ];

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

        $cols = [
            $myCol,
        ];

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

        $cols = [
            $myCol,
        ];

        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="text-align: right"', $html);

        $myCol->setType(new Type\PhpArray());
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<pre>First value</pre>', $html);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStyle()
    {
        $helper = new TableRow();

        // bold
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\Bold());

        $cols = [
            $myCol,
        ];

        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="font-weight: bold"', $html);

        // italic
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\Italic());

        $cols = [
            $myCol,
        ];
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="font-style: italic"', $html);

        // color
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\Color(AbstractColor::$RED));

        $cols = [
            $myCol,
        ];
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="color: #ff0000"', $html);

        // background color
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\BackgroundColor(AbstractColor::$GREEN));

        $cols = [
            $myCol,
        ];
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td style="background-color: #00ff00"', $html);

        // css class for cell
        $myCol = clone $this->myCol;
        $myCol->addStyle(new Style\CSSClass('test-class'));

        $cols = [
            $myCol,
        ];
        $html = $helper($this->rowWithId, $cols);
        $this->assertContains('<td class="test-class"', $html);

        // exception
        $style = $this->getMockForAbstractClass(\ZfcDatagrid\Column\Style\AbstractStyle::class);

        $myCol = clone $this->myCol;
        $myCol->addStyle($style);

        $cols = [
            $myCol,
        ];

        $html = $helper($this->rowWithId, $cols);
    }

    public function testAction()
    {
        $rowData           = $this->rowWithId;
        $rowData['action'] = '';

        $helper = new TableRow();

        // must be instanceof Column\Select...
        $myCol = new Column\Select('myCol');

        $action = new Column\Action\Checkbox();
        $action->setLink('http://example.com');

        $colAction = new Column\Action();
        $colAction->addAction($action);

        $cols = [
            $myCol,
            $colAction,
        ];

        $html = $helper($rowData, $cols);
        $this->assertContains('<input type="checkbox"', $html);

        // row action
        $cols = [
            $myCol,
            $colAction,
        ];
        $html = $helper($rowData, $cols, $action);
        $this->assertContains('<a href="http://example.com', $html);
    }
}
