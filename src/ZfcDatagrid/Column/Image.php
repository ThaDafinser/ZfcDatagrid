<?php
namespace ZfcDatagrid\Column;

/**
 * Display images
 * 
 * @deprecated
 */
class Image extends AbstractColumn
{

    public function __construct ($uniqueId = 'image')
    {
        $this->setUniqueId($uniqueId);
        
        $this->setUserSortDisabled(true);
        $this->setUserFilterDisabled(true);
    }
}
