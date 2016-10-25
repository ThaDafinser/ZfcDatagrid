<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Style\AbstractColor;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\AbstractColor
 */
class ColorTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $style = new AbstractColor(AbstractColor::$RED);

        $this->assertEquals(255, $style->getRed());
        $this->assertEquals(0, $style->getGreen());
        $this->assertEquals(0, $style->getBlue());
        $this->assertEquals('ff0000', $style->getRgbHexString());

        $style = new AbstractColor(AbstractColor::$GREEN);
        $this->assertEquals(0, $style->getRed());
        $this->assertEquals(255, $style->getGreen());
        $this->assertEquals(0, $style->getBlue());
        $this->assertEquals('00ff00', $style->getRgbHexString());

        $style = new AbstractColor(AbstractColor::$BLUE);
        $this->assertEquals(0, $style->getRed());
        $this->assertEquals(0, $style->getGreen());
        $this->assertEquals(255, $style->getBlue());
        $this->assertEquals('0000ff', $style->getRgbHexString());

        $style = new AbstractColor(50, 70, 30);
        $this->assertEquals(50, $style->getRed());
        $this->assertEquals(70, $style->getGreen());
        $this->assertEquals(30, $style->getBlue());
        $this->assertEquals('32461e', $style->getRgbHexString());
    }

    public function testSetRgb()
    {
        $style = new AbstractColor(0, 0, 0);

        $style->setRgb(20, 10, 5);
        $this->assertEquals(20, $style->getRed());
        $this->assertEquals(10, $style->getGreen());
        $this->assertEquals(5, $style->getBlue());

        $style->setRed(33);
        $this->assertEquals(33, $style->getRed());

        $style->setGreen(44);
        $this->assertEquals(44, $style->getGreen());

        $style->setBlue(55);
        $this->assertEquals(55, $style->getBlue());

        $this->assertEquals([
            'red'   => 33,
            'green' => 44,
            'blue'  => 55,
        ], $style->getRgbArray());
    }

    public function testRgbHexString()
    {
        $style = new AbstractColor(0, 0, 0);

        $this->assertEquals('000000', $style->getRgbHexString());

        $style->setRed(5);
        $this->assertEquals('050000', $style->getRgbHexString());

        $style->setGreen(6);
        $this->assertEquals('050600', $style->getRgbHexString());

        $style->setBlue(7);
        $this->assertEquals('050607', $style->getRgbHexString());
    }
}
