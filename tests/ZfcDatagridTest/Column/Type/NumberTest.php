<?php
namespace ZfcDatagridTest\Column\Type;

use ZfcDatagrid\Column\Type;
use PHPUnit_Framework_TestCase;

/**
 * @group Type
 *
 */
class NumberTest extends PHPUnit_Framework_TestCase
{

    public function testTypeName(){
        $type = new Type\Number();
        
        $this->assertEquals('number', $type->getTypeName());
    }
}