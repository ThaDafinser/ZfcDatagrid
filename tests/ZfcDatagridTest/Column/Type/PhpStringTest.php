<?php
namespace ZfcDatagridTest\Column\Type;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Column\Type;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Type\PhpString
 */
class PhpStringTest extends TestCase
{
    public function testTypeName()
    {
        $type = new Type\PhpString();

        $this->assertEquals('string', $type->getTypeName());
    }
}
