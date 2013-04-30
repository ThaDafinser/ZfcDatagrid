<?php
namespace ZfcDatagrid\Column\Type;

use Locale;
use NumberFormatter;

class Number implements TypeInterface
{

    /**
     * Locale to use instead of the default.
     *
     * @var string
     */
    protected $locale;

    /**
     * NumberFormat style to use.
     *
     * @var integer
     */
    protected $formatStyle;

    /**
     * NumberFormat type to use.
     *
     * @var integer
     */
    protected $formatType;

    protected $attributes = array();
    
    protected $prefix = '';
    
    protected $suffix = '';
    
    public function __construct ($formatStyle = NumberFormatter::DECIMAL, $formatType = NumberFormatter::TYPE_DEFAULT, $locale = null)
    {
        $this->formatStyle = $formatStyle;
        $this->formatType = $formatType;
        $this->locale = $locale;
    }

    public function setFormatStyle ($style = NumberFormatter::DECIMAL)
    {
        $this->formatStyle = $style;
    }

    public function getFormatStyle ()
    {
        return $this->formatStyle;
    }

    public function setFormatType ($type = NumberFormatter::TYPE_DEFAULT)
    {
        $this->formatType = $type;
    }

    public function getFormatType ()
    {
        return $this->formatType;
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
    
    /**
     * Set an attribute
     * @link http://www.php.net/manual/en/numberformatter.setattribute.php
     * @param attr int <p>
     * Attribute specifier - one of the
     * numeric attribute constants.
     * </p>
     * @param value int <p>
     * The attribute value.
     * </p>
     */
    public function addAttribute($attr, $value){
        $this->attributes[] = array('attribute' => $attr, 'value' => $value);
    }
    
    public function getAttributes(){
        return $this->attributes;
    }
    
    public function setSuffix($string = ''){
        $this->suffix = (string)$string;
    }
    
    public function getSuffix(){
        return $this->suffix;
    }
    
    public function setPrefix($string = ''){
        $this->prefix = (string)$string;
    }
    
    public function getPrefix(){
        return $this->prefix;
    }
}
