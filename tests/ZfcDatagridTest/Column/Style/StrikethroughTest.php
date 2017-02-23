<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Column\Style;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\Strikethrough
 */
class StrikethroughTest extends TestCase
{
    public function testCanCreateInstance()
    {
        $strikethrough = new Style\Strikethrough();
        $this->assertInstanceOf(Style\Strikethrough::class, $strikethrough);
    }
}
