<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Style;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\Strikethrough
 */
class StrikethroughTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateInstance()
    {
        $strikethrough = new Style\Strikethrough();
    }
}
