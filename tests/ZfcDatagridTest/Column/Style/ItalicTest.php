<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Style;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\Italic
 */
class ItalicTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateInstance()
    {
        $bold = new Style\Italic();
    }
}
