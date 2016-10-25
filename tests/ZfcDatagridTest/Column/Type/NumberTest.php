<?php
namespace ZfcDatagridTest\Column\Type;

use Locale;
use NumberFormatter;
use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Filter;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Type\Number
 */
class NumberTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Type\Number
     */
    private $numberFormatterAT;

    /**
     *
     * @var Type\Number
     */
    private $numberFormatterEN;

    public function setUp()
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $type = new Type\Number();
        $type->setLocale('de_AT');
        $this->numberFormatterAT = $type;

        $type = new Type\Number();
        $type->setLocale('en_US');
        $this->numberFormatterEN = $type;
    }

    public function testConstruct()
    {
        $type = new Type\Number();

        $this->assertEquals(NumberFormatter::DECIMAL, $type->getFormatStyle());
        $this->assertEquals(NumberFormatter::TYPE_DEFAULT, $type->getFormatType());
        $this->assertEquals(Locale::getDefault(), $type->getLocale());

        $this->assertEquals(Filter::EQUAL, $type->getFilterDefaultOperation());
    }

    public function testTypeName()
    {
        $type = new Type\Number();

        $this->assertEquals('number', $type->getTypeName());
    }

    public function testFormatStyle()
    {
        $type = new Type\Number();
        $type->setFormatStyle(NumberFormatter::CURRENCY);
        $this->assertEquals(NumberFormatter::CURRENCY, $type->getFormatStyle());
    }

    public function testFormatType()
    {
        $type = new Type\Number();
        $type->setFormatType(NumberFormatter::TYPE_DOUBLE);
        $this->assertEquals(NumberFormatter::TYPE_DOUBLE, $type->getFormatType());
    }

    public function testLocale()
    {
        $type = new Type\Number();
        $type->setLocale('de_AT');
        $this->assertEquals('de_AT', $type->getLocale());
    }

    public function testAttribute()
    {
        $type = new Type\Number();

        $this->assertCount(0, $type->getAttributes());

        $type->addAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);
        $this->assertCount(1, $type->getAttributes());
    }

    public function testSuffixPreffix()
    {
        $type = new Type\Number();

        $this->assertEquals('', $type->getPrefix());
        $this->assertEquals('', $type->getSuffix());

        $type->setPrefix('$');
        $this->assertEquals('$', $type->getPrefix());

        $type->setSuffix('EURO');
        $this->assertEquals('EURO', $type->getSuffix());
    }

    /**
     * Convert the user value to a filter value
     */
    public function testFilterValueAT()
    {
        $type = clone $this->numberFormatterAT;
        $this->assertEquals('23.15', $type->getFilterValue('23,15'));

        $type->setPrefix('€');
        $this->assertEquals('23.15', $type->getFilterValue('€23,15'));

        $type->setSuffix('#');
        $this->assertEquals('23.15', $type->getFilterValue('€23,15#'));
    }

    /**
     * Convert the user value to a filter value
     */
    public function testFilterValueEN()
    {
        $type = clone $this->numberFormatterEN;
        $this->assertEquals('23.15', $type->getFilterValue('23.15'));

        $type->setPrefix('€');
        $this->assertEquals('23.15', $type->getFilterValue('€23.15'));

        $type->setSuffix('#');
        $this->assertEquals('23.15', $type->getFilterValue('€23.15#'));
    }

    /**
     * Convert the database value to a display value
     */
    public function testUserValueAT()
    {
        $type = clone $this->numberFormatterAT;

        $this->assertEquals('23,15', $type->getUserValue(23.15));

        $type->setPrefix('€');
        $this->assertEquals('€23,15', $type->getUserValue(23.15));

        $type->setSuffix('#');
        $this->assertEquals('€23,15#', $type->getUserValue(23.15));
    }

    public function testWrongValues()
    {
        $type = clone $this->numberFormatterAT;

        // Print the user a 0
        $this->assertEquals('0', $type->getUserValue('myString'));

        // Filtering converting is dangerous, so keep the value...
        $this->assertEquals('myString', $type->getFilterValue('myString'));
    }
}
