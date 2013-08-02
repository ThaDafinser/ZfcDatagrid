<?php
namespace ZfcDatagridTest\Column\Action;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Action\Icon;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Action\Icon
 */
class IconTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct ()
    {
        $icon = new Icon();
        
        $this->assertEquals(array(), $icon->getAttributes());
    }
    
    public function testIconClass(){
        $icon = new Icon();

        $this->assertFalse($icon->hasIconClass());
        
        $icon->setIconClass('icon-add');
        $this->assertEquals('icon-add', $icon->getIconClass());
        $this->assertTrue($icon->hasIconClass());
        
        $this->assertEquals('<i title="" class="icon-add"></i>', $icon->toHtml());;
    }
    
    public function testIconLink ()
    {
        $icon = new Icon();
    
        $this->assertFalse($icon->hasIconLink());
    
        $icon->setIconLink('/images/21/add.png');
        $this->assertEquals('/images/21/add.png', $icon->getIconLink());
        $this->assertTrue($icon->hasIconLink());
        
        $this->assertEquals('<img src="/images/21/add.png" />', $icon->toHtml());
    }

    public function testException ()
    {
        $icon = new Icon();
        
        $this->setExpectedException('InvalidArgumentException');
        
        $icon->toHtml();
    }
}
