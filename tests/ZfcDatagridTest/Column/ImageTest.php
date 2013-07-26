<?php
namespace ZfcDatagridTest\Column;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Image;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Image
 */
class ImageTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct ()
    {
        $col = new Image();
        
        $this->assertEquals('image', $col->getUniqueId());
        $this->assertFalse($col->isUserSortEnabled());
        $this->assertFalse($col->isUserFilterEnabled());
    }
}