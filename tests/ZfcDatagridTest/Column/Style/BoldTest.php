<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit\Framework\TestCase;
use ZfcDatagrid\Column\Style;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\Bold
 */
class BoldTest extends TestCase
{
    public function testCanCreateInstance()
    {
        $bold = new Style\Bold();
        $this->assertInstanceOf(Style\Bold::class, $bold);
    }
}
