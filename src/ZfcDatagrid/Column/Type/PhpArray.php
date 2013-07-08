<?php
namespace ZfcDatagrid\Column\Type;

class PhpArray extends AbstractType
{

    public function getTypeName ()
    {
        return 'array';
    }

    /**
     *
     * @return array
     */
    public function getUserValue ($val)
    {
        if (! is_array($val)) {
            if ($val == '') {
                $val = array();
            } else {
                $val = explode(',', $val);
            }
        }
        
        return $val;
    }
}
