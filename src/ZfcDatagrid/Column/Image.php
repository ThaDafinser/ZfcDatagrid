<?php
namespace ZfcDatagrid\Column;

/**
 * Display images
 */
class Image extends AbstractColumn
{
    public function __construct($uniqueId){
        $this->setUniqueId($uniqueId);
        
        $this->setUserSortDisabled(true);
    }
}
