<?php
namespace ZfcDatagridTest\Column\Type;

use ZfcDatagrid\Column\Type;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Type\Image
 */
class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testTypeName()
    {
        $type = new Type\Image();

        $this->assertEquals('image', $type->getTypeName());
    }
}
