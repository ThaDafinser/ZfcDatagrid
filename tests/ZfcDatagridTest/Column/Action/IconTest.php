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

    public function testIcon ()
    {
        $icon = new Icon();
        $icon->setIconClass('icon-add');
        
        $this->assertEquals(array(
            'class' => 'icon-add'
        ), $icon->getAttributes());
        
        $icon->setIconLink('/images/icon/add.png');
        
        $html = '<i title="" class="icon-add"></i>';
        $this->assertEquals($html, $icon->toHtml());
    }
}
