<?php
namespace ZfcDatagrid\Column;

/**
 * This is just a BC compability class...please use Select instead!
 *
 * @dperecated
 *
 * @author kecmar
 *        
 */
class Standard extends Select
{

    public function __construct($columnOrIndexOrObject, $tableOrAliasOrUniqueId = null)
    {
        trigger_error('ZfcDatagrid\Column\Standard is deprecated, please use ZfcDatagrid\Column\Select instead', E_USER_DEPRECATED);
        
        parent::__construct($columnOrIndexOrObject, $tableOrAliasOrUniqueId);
    }
}
