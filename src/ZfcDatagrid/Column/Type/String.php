<?php
namespace ZfcDatagrid\Column\Type;

/**
 * Class String
 * @package ZfcDatagrid\Column\Type
 */
class String extends AbstractType
{
    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'string';
    }
}
