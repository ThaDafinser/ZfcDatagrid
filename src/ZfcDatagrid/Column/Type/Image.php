<?php
namespace ZfcDatagrid\Column\Type;


class Image extends AbstractType
{
    public function getTypeName(){
        return 'image';
    }
    
    /**
     * Convert the value from the source to the value, which the user will see
     *
     * @param string $val
     * @return string
     */
    public function getUserValue($val)
    {
        //@todo size...
        return '<img src="" />';
    }
}
