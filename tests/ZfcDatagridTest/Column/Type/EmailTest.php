<?php
namespace ZfcDatagridTest\Column\Type;

use ZfcDatagrid\Column\Type;
use PHPUnit_Framework_TestCase;

/**
 * @group Type
 *
 */
class EmailTest extends PHPUnit_Framework_TestCase
{

    public function testTypeName(){
        $type = new Type\Email();
        
        $this->assertEquals('email', $type->getTypeName());
    }
}