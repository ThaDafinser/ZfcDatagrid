<?php
namespace ZfcDatagridTest\Column\Type;

use ZfcDatagrid\Column\Type;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Type\String
 */
class StringTest extends PHPUnit_Framework_TestCase
{
    public function testTypeName()
    {
        $type = new Type\String();

        $this->assertEquals('string', $type->getTypeName());
    }
}
