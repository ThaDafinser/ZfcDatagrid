<?php
namespace ZfcDatagrid\Column\Action;

abstract class AbstractAction
{

    protected $label = '';

    protected $iconClass;

    protected $link = '#';
    
    public function setLabel ($name)
    {
        $this->label = (string) $name;
    }

    /**
     *
     * @return string
     */
    public function getLabel ()
    {
        return $this->label;
    }
    
    public function setLink($href){
        $this->link = (string)$href;
    }
    
    public function getLink(){
        return $this->link;
    }

    public function setIconClass ($className)
    {
        $this->iconClass = (string) $className;
    }

    public function getIconClass ()
    {
        return $this->iconClass;
    }
    
    public function hasIconClass(){
        if($this->iconClass != ''){
            return true;
        }
        
        return false;
    }
}
