<?php
namespace ZfcDatagridTest\Column;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Icon;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Icon
 */
class IconTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct ()
    {
        $col = new Icon();
        
        $this->assertEquals('icon', $col->getUniqueId());
        $this->assertFalse($col->isUserSortEnabled());
        $this->assertFalse($col->isUserFilterEnabled());
        $this->assertEquals(5, $col->getWidth());
    }

    public function testIconClass ()
    {
        $col = new Icon();
        
        $this->assertFalse($col->hasIconClass());
        
        $col->setIconClass('icon-add');
        $this->assertEquals('icon-add', $col->getIconClass());
        $this->assertTrue($col->hasIconClass());
    }

    public function testIconLink ()
    {
        $col = new Icon();
        
        $this->assertFalse($col->hasIconLink());
        
        $col->setIconLink('/images/21/add.png');
        $this->assertEquals('/images/21/add.png', $col->getIconLink());
        $this->assertTrue($col->hasIconLink());
    }

    public function testTitle ()
    {
        $col = new Icon();
        
        $this->assertFalse($col->hasTitle());
        $this->assertEquals('', $col->getTitle());
        
        $col->setTitle('blubb');
        $this->assertEquals('blubb', $col->getTitle());
        $this->assertTrue($col->hasTitle());
    }
}