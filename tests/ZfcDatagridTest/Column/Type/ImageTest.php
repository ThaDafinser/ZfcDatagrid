<?php
namespace ZfcDatagridTest\Column\Type;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Type;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Type\Image
 */
class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testTypeName()
    {
        $type = new Type\Image();

        $this->assertEquals('image', $type->getTypeName());
    }
}
