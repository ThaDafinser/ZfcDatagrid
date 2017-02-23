<?php
namespace ZfcDatagridTest\Column\Type;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Column\Type;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Type\PhpArray
 */
class PhpArrayTest extends TestCase
{
    public function testTypeName()
    {
        $type = new Type\PhpArray();

        $this->assertEquals('array', $type->getTypeName());
    }

    public function testUserValue()
    {
        $type = new Type\PhpArray();

        $value = '1,2,3';
        $this->assertEquals([
            1,
            2,
            3,
        ], $type->getUserValue($value));

        $value = '';
        $this->assertEquals([], $type->getUserValue($value));

        $value = [
            1,
            2,
            3,
        ];
        $this->assertEquals([
            1,
            2,
            3,
        ], $type->getUserValue($value));
    }
}
