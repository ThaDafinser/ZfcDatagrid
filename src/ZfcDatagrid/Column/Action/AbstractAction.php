<?php
namespace ZfcDatagrid\Column\Action;

use ZfcDatagrid\Column;

abstract class AbstractAction
{

    protected $label = '';

    protected $link = '#';

    protected $htmlAttributes = array();

    protected $showOnValues = array();

    public function __construct ()
    {
        $this->htmlAttributes['class'] = array(
            'btn'
        );
    }

    /**
     *
     * @param string $name            
     */
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

    /**
     *
     * @param string $name            
     */
    public function setTitle ($name)
    {
        $this->setAttribute('title', (string) $name);
    }

    /**
     *
     * @param string $href            
     */
    public function setLink ($href)
    {
        $this->link = (string) $href;
    }

    /**
     *
     * @return string
     */
    public function getLink ()
    {
        return $this->link;
    }

    /**
     * Set a HTML attributes
     *
     * @param string $name            
     * @param string $value            
     */
    public function setAttribute ($name, $value)
    {
        $this->htmlAttributes[$name] = $value;
    }
    
    public function getAttribute($name){
        if(isset($this->htmlAttributes[$name])){
            return $this->htmlAttributes[$name];
        }
        
        return '';
    }

    public function getAttributes ()
    {
        return $this->htmlAttributes;
    }

    /**
     * Add a css class
     *
     * @param string $className            
     */
    public function addClass ($className)
    {
        $attr = $this->getAttributes();
        if (! isset($attr['class'])) {
            $attr['class'] = array();
        }
        
        $class = $attr['class'];
        $class[] = (string) $className;
        
        $this->setAttribute('class', $class);
    }

    public function addShowOnValue (Column\AbstractColumn $col, $value = null)
    {
        $this->showOnValues[] = array(
            'column' => $col,
            'value' => $value
        );
    }

    /**
     *
     * @return array
     */
    public function getShowOnValues ()
    {
        return $this->showOnValues;
    }

    /**
     *
     * @return boolean
     */
    public function hasShowOnValues ()
    {
        if (count($this->showOnValues) > 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Display this action on this row?
     *
     * @param array $row            
     * @return boolean
     */
    public function isDisplayed (array $row)
    {
        if ($this->hasShowOnValues() === true) {
            foreach ($this->getShowOnValues() as $showOnValue) {
                if ($showOnValue['value'] === $row[$showOnValue['column']->getUniqueId()]) {
                    return true;
                }
            }
        } else {
            return true;
        }
        
        return false;
    }

    /**
     * 
     * @return string
     */
    public function toHtml ()
    {
        $attributes = array();
        foreach ($this->getAttributes() as $attrKey => $attrValue) {
            if (is_array($attrValue)) {
                $attrValue = implode(' ', $attrValue);
            }
            $attributes[] = $attrKey . '="' . $attrValue . '"';
        }
        
        $attributes = implode(' ', $attributes);
        
        return '<a href="' . $this->getLink() . '" ' . $attributes . '>' . $this->getLabel() . '</a>';
    }
}
