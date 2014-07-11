<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Style\CSSClass;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Style\CSSClass
 */
class CSSClassTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $testClass = 'test-class';
        $style = new CSSClass($testClass);
        $this->assertEquals($testClass, $style->getClass());
        $this->assertEquals(false, $style->getForRow());

        $style = new CSSClass($testClass, true);
        $this->assertEquals($testClass, $style->getClass());
        $this->assertEquals(true, $style->getForRow());

    }

    public function testSetClass()
    {
        $style = new CSSClass('something');

        $style->setClass('else');
        $this->assertEquals('else', $style->getClass());

        $style->setClass('another');
        $this->assertEquals('another', $style->getClass());

        $cssStyles = array('first', 'second');
        $style->setClass($cssStyles);
        $this->assertEquals(implode(' ', $cssStyles), $style->getClass());
    }


    public function testForRow()
    {
        $style = new CSSClass('something');

        $this->assertFalse($style->getForRow());

        $style->setForRow(true);
        $this->assertTrue($style->getForRow());
    }
}
