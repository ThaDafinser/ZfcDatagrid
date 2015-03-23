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

        $this->assertEquals(array(
            'href' => '#',
            'class' => 'btn',
        ), $button->getAttributes());
    }

    public function testLabel()
    {
        $button = new Button();

        $button->setLabel('My label');
        $this->assertEquals('My label', $button->getLabel());

        $html = '<a href="#" class="btn">My label</a>';
        $this->assertEquals($html, $button->toHtml(array()));
    }

    public function testHtmlException()
    {
        $button = new Button();

        $this->setExpectedException('InvalidArgumentException');
        $button->toHtml(array());
    }
}
