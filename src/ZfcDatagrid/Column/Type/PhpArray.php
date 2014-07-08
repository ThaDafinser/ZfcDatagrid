<?php
namespace ZfcDatagrid\Column\Type;

/**
 * Class PhpArray
 * @package ZfcDatagrid\Column\Type
 */
class PhpArray extends AbstractType
{

    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'array';
    }

    /**
     * Convert a value into an array
     *
     * @param  mixed $value
     * @return array
     */
    public function getUserValue($value)
    {
        if (! is_array($value)) {
            if ($value == '') {
                $value = array();
            } else {
                $value = explode(',', $value);
            }
        }

        return $value;
    }
}
