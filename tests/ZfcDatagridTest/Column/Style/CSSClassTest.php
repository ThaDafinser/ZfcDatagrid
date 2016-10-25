<?php
namespace ZfcDatagridTest\Column\Style;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Style\CSSClass;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Style\CSSClass
 */
class CSSClassTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $testClass = 'test-class';
        $style     = new CSSClass($testClass);
        $this->assertEquals($testClass, $style->getClass());
    }

    public function testSetClass()
    {
        $style = new CSSClass('something');

        $style->setClass('else');
        $this->assertEquals('else', $style->getClass());

        $style->setClass('another');
        $this->assertEquals('another', $style->getClass());

        $cssStyles = ['first', 'second'];
        $style->setClass($cssStyles);
        $this->assertEquals(implode(' ', $cssStyles), $style->getClass());
    }
}
