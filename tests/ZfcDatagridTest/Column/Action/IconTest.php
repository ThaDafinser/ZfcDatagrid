<?php
namespace ZfcDatagridTest\Column\Action;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Action\Icon;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Action\Icon
 */
class IconTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $icon = new Icon();

        $this->assertEquals([
            'href' => '#',
        ], $icon->getAttributes());
    }

    public function testIconClass()
    {
        $icon = new Icon();

        $this->assertFalse($icon->hasIconClass());

        $icon->setIconClass('icon-add');
        $this->assertEquals('icon-add', $icon->getIconClass());
        $this->assertTrue($icon->hasIconClass());

        $this->assertEquals('<a href="#"><i class="icon-add"></i></a>', $icon->toHtml([]));
    }

    public function testIconLink()
    {
        $icon = new Icon();

        $this->assertFalse($icon->hasIconLink());

        $icon->setIconLink('/images/21/add.png');
        $this->assertEquals('/images/21/add.png', $icon->getIconLink());
        $this->assertTrue($icon->hasIconLink());

        $this->assertEquals('<a href="#"><img src="/images/21/add.png" /></a>', $icon->toHtml([]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testException()
    {
        $icon = new Icon();

        $icon->toHtml([]);
    }
}
