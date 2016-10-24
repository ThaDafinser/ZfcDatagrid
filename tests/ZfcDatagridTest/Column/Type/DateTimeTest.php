<?php
namespace ZfcDatagridTest\Column\Type;

use IntlDateFormatter;
use Locale;
use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Filter;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Type\DateTime
 */
class DateTimeTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Type\DateTime
     */
    private $datetimeAT;

    /**
     *
     * @var Type\DateTime
     */
    private $datetimeEN;

    public function setUp()
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $type = new Type\DateTime();
        $type->setLocale('de_AT');
        $type->setSourceTimezone('UTC');
        $type->setOutputTimezone('UTC');
        $this->datetimeAT = $type;

        $type = new Type\DateTime();
        $type->setLocale('en_US');
        $type->setSourceTimezone('UTC');
        $type->setOutputTimezone('UTC');
        $this->datetimeEN = $type;
    }

    public function testConstruct()
    {
        $type = new Type\DateTime();

        $this->assertEquals('Y-m-d H:i:s', $type->getSourceDateTimeFormat());
        $this->assertEquals(IntlDateFormatter::MEDIUM, $type->getOutputDateType());
        $this->assertEquals(IntlDateFormatter::NONE, $type->getOutputTimeType());
        $this->assertEquals(Locale::getDefault(), $type->getLocale());

        $this->assertEquals('UTC', $type->getSourceTimezone());
        $this->assertEquals(date_default_timezone_get(), $type->getOutputTimezone());

        $this->assertEquals(Filter::GREATER_EQUAL, $type->getFilterDefaultOperation());
    }

    public function testTypeName()
    {
        $type = new Type\DateTime();

        $this->assertEquals('dateTime', $type->getTypeName());
    }

    public function testSourceDateTimeFormat()
    {
        $type = new Type\DateTime();

        $type->setSourceDateTimeFormat('Y-m-d');
        $this->assertEquals('Y-m-d', $type->getSourceDateTimeFormat());
    }

    public function testOutputDateType()
    {
        $type = new Type\DateTime();

        $type->setOutputDateType(IntlDateFormatter::FULL);
        $this->assertEquals(IntlDateFormatter::FULL, $type->getOutputDateType());
    }

    public function testOutputTimeType()
    {
        $type = new Type\DateTime();

        $type->setOutputTimeType(IntlDateFormatter::SHORT);
        $this->assertEquals(IntlDateFormatter::SHORT, $type->getOutputTimeType());
    }

    public function testLocale()
    {
        $type = new Type\DateTime();

        $type->setLocale('de_AT');
        $this->assertEquals('de_AT', $type->getLocale());
    }

    public function testSourceTimezone()
    {
        $type = new Type\DateTime();

        $type->setSourceTimezone('Europe/Vienna');
        $this->assertEquals('Europe/Vienna', $type->getSourceTimezone());
    }

    public function testOutputTimezone()
    {
        $type = new Type\DateTime();

        $type->setOutputTimezone('Europe/Vaduz');
        $this->assertEquals('Europe/Vaduz', $type->getOutputTimezone());
    }

    public function testOutputPattern()
    {
        $type = new Type\DateTime();

        $type->setOutputPattern('yyyymmdd hh:mm:ss z');
        $this->assertEquals('yyyymmdd hh:mm:ss z', $type->getOutputPattern());
    }

    /**
     * Convert the user value to a filter value
     */
    public function testFilterValueAT()
    {
        $type = clone $this->datetimeAT;
        $this->assertEquals('2013-01-10 00:00:00', $type->getFilterValue('10.01.2013'));

        $type->setOutputTimeType(IntlDateFormatter::SHORT);
        $this->assertEquals('2013-01-10 10:00:00', $type->getFilterValue('10.01.2013 10:00'));
    }

    /**
     * Convert the user value to a filter value
     */
    public function testFilterValueEN()
    {
        $type = clone $this->datetimeEN;
        $this->assertEquals('2013-01-10 00:00:00', $type->getFilterValue('Jan 10, 2013'));

        $type->setOutputTimeType(IntlDateFormatter::SHORT);

        $this->assertEquals('2013-01-10 10:00:00', $type->getFilterValue('Jan 10, 2013 10:00 AM'));
    }

    /**
     * Convert the database value to a display value
     */
    public function testUserValueAT()
    {
        $type = clone $this->datetimeAT;
        $this->assertEquals('', $type->getUserValue(''));

        $this->assertEquals('10.01.2013', $type->getUserValue(new \DateTime('2013-01-10 12:00:00')), 'Compare DateTime');
        $this->assertEquals('10.01.2013', $type->getUserValue('2013-01-10 00:00:00'), 'Compare string');

        $type->setOutputTimeType(IntlDateFormatter::SHORT);
        $this->assertEquals('10.01.2013 10:00', $type->getUserValue('2013-01-10 10:00:00'));
    }

    /**
     * Convert the database value to a display value
     */
    public function testUserValueEN()
    {
        $type = clone $this->datetimeEN;
        $this->assertEquals('Jan 10, 2013', $type->getUserValue('2013-01-10 00:00:00'));

        $type->setOutputTimeType(IntlDateFormatter::SHORT);
        $this->assertEquals('Jan 10, 2013 10:00 AM', $type->getUserValue('2013-01-10 10:00:00'));
    }
}
