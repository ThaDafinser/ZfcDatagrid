<?php
namespace ZfcDatagridTest\Column\Type;

use ZfcDatagrid\Column\Type;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Type\PhpArray
 */
class PhpArrayTest extends PHPUnit_Framework_TestCase
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
        $this->assertEquals(array(
            1,
            2,
            3,
        ), $type->getUserValue($value));

        $value = '';
        $this->assertEquals(array(), $type->getUserValue($value));

        $value = array(
            1,
            2,
            3,
        );
        $this->assertEquals(array(
            1,
            2,
            3,
        ), $type->getUserValue($value));
    }
}
