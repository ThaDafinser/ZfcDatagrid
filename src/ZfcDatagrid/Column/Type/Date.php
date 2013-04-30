<?php
namespace ZfcDatagrid\Column\Type;

use DateTime;
use IntlDateFormatter;
use Locale;

class Date implements TypeInterface
{

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
     *
     * @param string $sourceDateTimeFormat
     *            PHP DateTime format
     * @param unknown $outputDateType            
     * @param unknown $outputTimeType            
     * @param string $locale            
     * @param string $sourceTimezone            
     * @param string $outputTimezone            
     */
    public function __construct ($sourceDateTimeFormat = 'Y-m-d', $outputDateType = IntlDateFormatter::MEDIUM, $outputTimeType = IntlDateFormatter::NONE, $locale = null, $sourceTimezone = 'UTC', $outputTimezone = null)
    {
        $this->sourceDateTimeFormat = $sourceDateTimeFormat;
        $this->outputDateType = $outputDateType;
        $this->outputTimeType = $outputTimeType;
        $this->locale = $locale;
        $this->sourceTimezone = $sourceTimezone;
        $this->outputTimezone = $outputTimezone;
    }

    public function setSourceDateTimeFormat ($format = 'Y-m-d')
    {
        $this->sourceDateTimeFormat = $format;
    }

    public function getSourceDateTimeFormat ()
    {
        return $this->sourceDateTimeFormat;
    }

    public function setOutputDateType ($dateType = IntlDateFormatter::MEDIUM)
    {
        $this->outputDateType = $dateType;
    }

    public function getOutputDateType ()
    {
        return $this->outputDateType;
    }

    public function setOutputTimeType ($timeType = IntlDateFormatter::NONE)
    {
        $this->outputTimeType = $timeType;
    }

    public function getOutputTimeType ()
    {
        return $this->outputTimeType;
    }

    public function setLocale ($locale = null)
    {
        $this->locale = $locale;
    }

    public function getLocale ()
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }
        
        return $this->locale;
    }

    public function setSourceTimezone ($timezone = 'UTC')
    {
        $this->sourceTimezone = $timezone;
    }

    public function getSourceTimezone ()
    {
        return $this->sourceTimezone;
    }
    
    public function setOutputTimezone($timezone = null){
        $this->outputTimezone = $timezone;
    }

    public function getOutputTimezone ()
    {
        if($this->outputTimezone === null){
            $this->outputTimezone = date_default_timezone_get();
        }
        
        return $this->outputTimezone;
    }

    /**
     * ATTENTION: IntlDateTimeFormatter FORMAT!
     * 
     * @param string $pattern
     */
    public function setOutputPattern($pattern = null){
        $this->outputPattern = $pattern;
    }
    
    public function getOutputPattern ()
    {
        return $this->outputPattern;
    }
}
