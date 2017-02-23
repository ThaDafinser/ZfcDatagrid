<?php
namespace ZfcDatagridTest\Column\Type;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Column\Type;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Type\Image
 */
class ImageTest extends TestCase
{
    public function testTypeName()
    {
        $type = new Type\Image();

        $this->assertEquals('image', $type->getTypeName());
    }
}
