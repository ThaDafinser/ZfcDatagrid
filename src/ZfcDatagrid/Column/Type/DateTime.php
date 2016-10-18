<?php

namespace ZfcDatagrid\Column\Type;

use DateTime as PhpDateTime;
use DateTimeZone;
use IntlDateFormatter;
use Locale;
use ZfcDatagrid\Filter;

class DateTime extends AbstractType
{
    protected $daterangePickerEnabled = false;

    protected $sourceDateTimeFormat;

    protected $outputDateType;

    protected $outputTimeType;

    /**
     * Locale to use instead of the default.
     *
     * @var string
     */
    protected $locale;

    /**
     * Timezone to use.
     *
     * @var string
     */
    protected $sourceTimezone;

    /**
     * Timezone to use.
     *
     * @var string
     */
    protected $outputTimezone;

    protected $outputPattern;

    /**
     * @param string $sourceDateTimeFormat
     *                                     PHP DateTime format
     * @param int    $outputDateType
     * @param int    $outputTimeType
     * @param string $locale
     * @param string $sourceTimezone
     * @param string $outputTimezone
     */
    public function __construct($sourceDateTimeFormat = 'Y-m-d H:i:s', $outputDateType = IntlDateFormatter::MEDIUM, $outputTimeType = IntlDateFormatter::NONE, $locale = null, $sourceTimezone = 'UTC', $outputTimezone = null)
    {
        $this->setSourceDateTimeFormat($sourceDateTimeFormat);
        $this->setOutputDateType($outputDateType);
        $this->setOutputTimeType($outputTimeType);
        $this->setLocale($locale);
        $this->setSourceTimezone($sourceTimezone);
        $this->setOutputTimezone($outputTimezone);
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'dateTime';
    }

    /**
     * Set Daterange Filter enabled true/false.
     *
     * @param bool $val
     */
    public function setDaterangePickerEnabled($val = true)
    {
        $this->daterangePickerEnabled = $val;
    }

    /**
     * Check if the Daterange Filter is enabled.
     */
    public function isDaterangePickerEnabled()
    {
        return $this->daterangePickerEnabled;
    }

    public function setSourceDateTimeFormat($format = 'Y-m-d H:i:s')
    {
        $this->sourceDateTimeFormat = $format;
    }

    public function getSourceDateTimeFormat()
    {
        return $this->sourceDateTimeFormat;
    }

    public function setOutputDateType($dateType = IntlDateFormatter::MEDIUM)
    {
        $this->outputDateType = $dateType;
    }

    public function getOutputDateType()
    {
        return $this->outputDateType;
    }

    public function setOutputTimeType($timeType = IntlDateFormatter::NONE)
    {
        $this->outputTimeType = $timeType;
    }

    public function getOutputTimeType()
    {
        return $this->outputTimeType;
    }

    public function setLocale($locale = null)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        if (null === $this->locale) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    public function setSourceTimezone($timezone = 'UTC')
    {
        $this->sourceTimezone = $timezone;
    }

    public function getSourceTimezone()
    {
        return $this->sourceTimezone;
    }

    public function setOutputTimezone($timezone = null)
    {
        $this->outputTimezone = $timezone;
    }

    public function getOutputTimezone()
    {
        if (null === $this->outputTimezone) {
            $this->outputTimezone = date_default_timezone_get();
        }

        return $this->outputTimezone;
    }

    /**
     * ATTENTION: IntlDateTimeFormatter FORMAT!
     *
     * @param string $pattern
     */
    public function setOutputPattern($pattern = null)
    {
        $this->outputPattern = $pattern;
    }

    public function getOutputPattern()
    {
        return $this->outputPattern;
    }

    public function getFilterDefaultOperation()
    {
        return Filter::GREATER_EQUAL;
    }

    /**
     * @param string $val
     *
     * @return string
     */
    public function getFilterValue($val)
    {
        $formatter = new IntlDateFormatter($this->getLocale(), $this->getOutputDateType(), $this->getOutputTimeType(), $this->getOutputTimezone(), IntlDateFormatter::GREGORIAN, $this->getOutputPattern());
        $timestamp = $formatter->parse($val);

        $date = new PhpDateTime();
        $date->setTimestamp($timestamp);
        $date->setTimezone(new DateTimeZone($this->getSourceTimezone()));

        return $date->format($this->getSourceDateTimeFormat());
    }

    /**
     * Convert the value from the source to the value, which the user will see in the column.
     *
     * @param mixed $val
     *
     * @return string
     */
    public function getUserValue($val)
    {
        if ('' == $val) {
            return '';
        }

        if ($val instanceof PhpDateTime) {
            $date = $val;
            $date->setTimezone(new DateTimeZone($this->getSourceTimezone()));
            $date->setTimezone(new DateTimeZone($this->getOutputTimezone()));
        } else {
            $date = PhpDateTime::createFromFormat($this->getSourceDateTimeFormat(), $val, new DateTimeZone($this->getSourceTimezone()));
            if (false === $date) {
                return '';
            }
            $date->setTimezone(new DateTimeZone($this->getOutputTimezone()));
        }
        $formatter = new IntlDateFormatter($this->getLocale(), $this->getOutputDateType(), $this->getOutputTimeType(), $this->getOutputTimezone(), IntlDateFormatter::GREGORIAN, $this->getOutputPattern());

        return $formatter->format($date->getTimestamp());
    }
}
