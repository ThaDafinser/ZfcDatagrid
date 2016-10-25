<?php
namespace ZfcDatagridTest\Column\Type;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Type;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Type\PhpString
 */
class PhpStringTest extends PHPUnit_Framework_TestCase
{
    public function testTypeName()
    {
        $type = new Type\PhpString();

        $this->assertEquals('string', $type->getTypeName());
    }
}
