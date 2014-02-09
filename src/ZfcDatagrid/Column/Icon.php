<?php
namespace ZfcDatagrid\Column;

/**
 * Display icons
 *
 * @deprecated
 *
 */
class Icon extends AbstractColumn
{

    protected $iconClass;

    protected $iconLink;

    protected $title = '';

    public function __construct($uniqueId = 'icon')
    {
        $this->setUniqueId($uniqueId);
        
        $this->setUserSortDisabled(true);
        $this->setUserFilterDisabled(true);
        $this->setWidth(5);
    }

    public function setIconClass($name)
    {
        $this->iconClass = (string) $name;
    }

    public function getIconClass()
    {
        return $this->iconClass;
    }

    public function hasIconClass()
    {
        if ($this->getIconClass() != '') {
            return true;
        }
        
        return false;
    }

    public function setIconLink($http)
    {
        $this->iconLink = $http;
    }

    public function getIconLink()
    {
        return $this->iconLink;
    }

    public function hasIconLink()
    {
        if ($this->getIconLink() != '') {
            return true;
        }
        
        return false;
    }

    public function setTitle($title)
    {
        $this->title = (string) $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function hasTitle()
    {
        if ($this->getTitle() != '') {
            return true;
        }
        
        return false;
    }
}
