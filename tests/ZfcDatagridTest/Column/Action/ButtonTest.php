<?php
namespace ZfcDatagridTest\Column\Action;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Action\Button;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Action\Button
 */
class ButtonTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $button = new Button();

        $this->assertEquals([
            'href'  => '#',
            'class' => 'btn',
        ], $button->getAttributes());
    }

    public function testLabelAndToHtml()
    {
        $button = new Button();

        $button->setLabel('My label');
        $this->assertEquals('My label', $button->getLabel());

        $html = '<a href="#" class="btn">My label</a>';
        $this->assertEquals($html, $button->toHtml([]));
    }

    public function testColumnLabelAndToHtml()
    {
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->setUniqueId('myCol');

        $button = new Button();

        $button->setLabel($col);
        $this->assertInstanceOf('ZfcDatagrid\Column\AbstractColumn', $button->getLabel());

        $html = '<a href="#" class="btn">Blubb</a>';
        $this->assertEquals($html, $button->toHtml(['myCol' => 'Blubb']));
    }

    public function testHtmlException()
    {
        $button = new Button();

        $this->setExpectedException('InvalidArgumentException');
        $button->toHtml([]);
    }
}
