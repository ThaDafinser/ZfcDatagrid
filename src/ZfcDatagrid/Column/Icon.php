<?php
namespace ZfcDatagrid\Column;

/**
 * Display differnt icons to a value or general
 */
class Icon extends AbstractColumn
{

    private $icons = array();

    public function __construct ($uniqueId = 'icon')
    {
        $this->setUniqueId($uniqueId);
        
        $this->setUserSortDisabled(true);
        $this->setUserFilterDisabled(true);
    }

    public function addIcon ($httpLink, $showByValue = '')
    {
        $this->icons[] = array(
            'link' => $httpLink,
            'showByValue' => $showByValue
        );
    }
}
