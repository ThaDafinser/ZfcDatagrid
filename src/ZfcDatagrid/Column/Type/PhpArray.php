<?php

namespace ZfcDatagrid\Column\Type;

class PhpArray extends AbstractType
{
    /**
     * Separator of the string to be used to explode the array.
     *
     * @var string
     */
    protected $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator = ',')
    {
        $this->setSeparator($separator);
    }

    /**
     * Set separator of the string to be used to explode the array.
     *
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }

    /*
     * Get the string separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    public function getTypeName()
    {
        return 'array';
    }

    /**
     * Convert a value into an array.
     *
     * @param mixed $value
     *
     * @return array
     */
    public function getUserValue($value)
    {
        if (!is_array($value)) {
            if ('' == $value) {
                $value = [];
            } else {
                $value = explode($this->getSeparator(), $value);
            }
        }

        return $value;
    }
}
