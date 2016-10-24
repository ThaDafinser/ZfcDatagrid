<?php
namespace ZfcDatagridTest\Column\Action;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Action\Button;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Action\Button
 */
class ButtonTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $button = new Button();

        $this->assertEquals([
            'href'  => '#',
            'class' => 'btn btn-default',
        ], $button->getAttributes());
    }

    public function testLabelAndToHtml()
    {
        $button = new Button();

        $button->setLabel('My label');
        $this->assertEquals('My label', $button->getLabel());

        $html = '<a href="#" class="btn btn-default">My label</a>';
        $this->assertEquals($html, $button->toHtml([]));
    }

    public function testColumnLabelAndToHtml()
    {
        $col = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col->setUniqueId('myCol');

        $button = new Button();

        $button->setLabel($col);
        $this->assertInstanceOf(\ZfcDatagrid\Column\AbstractColumn::class, $button->getLabel());

        $html = '<a href="#" class="btn btn-default">Blubb</a>';
        $this->assertEquals($html, $button->toHtml(['myCol' => 'Blubb']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHtmlException()
    {
        $button = new Button();

        $button->toHtml([]);
    }
}
