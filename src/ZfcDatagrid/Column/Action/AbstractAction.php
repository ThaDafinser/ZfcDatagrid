<?php
namespace ZfcDatagrid\Column\Action;

use ZfcDatagrid\Column;

abstract class AbstractAction
{

    protected $label = '';

    protected $link = '#';
    
    protected $iconClass;

    protected $htmlAttributes = array();
    
    protected $showOnValues = array();
    
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
    
    public function setHtmlAttributes($name, $value){
        $this->htmlAttributes[$name] = $value;
    }
    
    public function getHtmlAttributes(){
        return $this->htmlAttributes;
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
    
    public function addShowOnValue(Column\AbstractColumn $col, $value = null){
        $this->showOnValues[] = array('column' => $col, 'value' => $value);
    }
    
    public function getShowOnValues(){
        return $this->showOnValues;
    }
    
    public function hasShowOnValues(){
        if(count($this->showOnValues) > 0){
            return true;
        }
        
        return false;
    }
}
