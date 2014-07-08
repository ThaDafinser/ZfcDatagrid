<?php
namespace ZfcDatagrid\Column\Type;

use ZfcDatagrid\Filter;
use Locale;
use NumberFormatter;

/**
 * Class Number
 * @package ZfcDatagrid\Column\Type
 */
class Number extends AbstractType
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

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var string
     */
    protected $suffix = '';

    /**
     * @param int  $formatStyle
     * @param int  $formatType
     * @param null $locale
     */
    public function __construct($formatStyle = NumberFormatter::DECIMAL, $formatType = NumberFormatter::TYPE_DEFAULT, $locale = null)
    {
        $this->setFormatStyle($formatStyle);
        $this->setFormatType($formatType);
        $this->setLocale($locale);
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'number';
    }

    /**
     * @param int $style
     *
     * @return $this
     */
    public function setFormatStyle($style = NumberFormatter::DECIMAL)
    {
        $this->formatStyle = $style;

        return $this;
    }

    /**
     * @return int
     */
    public function getFormatStyle()
    {
        return $this->formatStyle;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setFormatType($type = NumberFormatter::TYPE_DEFAULT)
    {
        $this->formatType = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getFormatType()
    {
        return $this->formatType;
    }

    /**
     * @param null $locale
     *
     * @return $this
     */
    public function setLocale($locale = null)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Set an attribute
     *
     * @link http://www.php.net/manual/en/numberformatter.setattribute.php
     * @param
     *            attr int <p>
     *            Attribute specifier - one of the
     *            numeric attribute constants.
     *            </p>
     * @param
     *            value int <p>
     *            The attribute value.
     *            </p>
     */
    public function addAttribute($attr, $value)
    {
        $this->attributes[] = array(
            'attribute' => $attr,
            'value' => $value
        );
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function setSuffix($string = '')
    {
        $this->suffix = (string) $string;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function setPrefix($string = '')
    {
        $this->prefix = (string) $string;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getFilterDefaultOperation()
    {
        return Filter::EQUAL;
    }

    /**
     *
     * @param  string $val
     * @return string
     */
    public function getFilterValue($val)
    {
        $formatter = new NumberFormatter($this->getLocale(), $this->getFormatStyle());
        foreach ($this->getAttributes() as $attribute) {
            $formatter->setAttribute($attribute['attribute'], $attribute['value']);
        }

        if (strlen($this->getPrefix()) > 0 && strpos($val, $this->getPrefix()) === 0) {
            $val = substr($val, strlen($this->getPrefix()));
        }
        if (strlen($this->getSuffix()) > 0 && strpos($val, $this->getSuffix()) > 0) {
            $val = substr($val, 0, - strlen($this->getSuffix()));
        }

        try {
            $formattedValue = $formatter->parse($val);
        } catch (\Exception $e) {
            return $val;
        }

        if ($formattedValue === false) {
            return $val;
        }

        return $formattedValue;
    }

    /**
     * Convert the value from the source to the value, which the user will see
     *
     * @param  string $val
     * @return string
     */
    public function getUserValue($val)
    {
        $formatter = new NumberFormatter($this->getLocale(), $this->getFormatStyle());
        foreach ($this->getAttributes() as $attribute) {
            $formatter->setAttribute($attribute['attribute'], $attribute['value']);
        }

        $formattedValue = $formatter->format($val, $this->getFormatType());

        return (string) $this->getPrefix() . $formattedValue . $this->getSuffix();
    }
}
