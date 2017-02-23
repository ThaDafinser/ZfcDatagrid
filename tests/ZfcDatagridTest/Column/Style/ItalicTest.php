<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Column\Style;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\Italic
 */
class ItalicTest extends TestCase
{
    public function testCanCreateInstance()
    {
        $bold = new Style\Italic();
        $this->assertInstanceOf(Style\Italic::class, $bold);
    }
}
