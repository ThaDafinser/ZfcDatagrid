<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Style;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\Bold
 */
class BoldTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateInstance()
    {
        $bold = new Style\Bold();
    }
}
