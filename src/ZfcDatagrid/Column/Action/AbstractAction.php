<?php
namespace ZfcDatagrid\Column\Action;

use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

abstract class AbstractAction
{

    protected $link = '#';

    protected $title = '';

    protected $htmlAttributes = array();

    protected $showOnValues = array();

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

    public function getAttribute ($name)
    {
        if (isset($this->htmlAttributes[$name])) {
            return $this->htmlAttributes[$name];
        }
        
        return '';
    }

    public function getAttributes ()
    {
        return $this->htmlAttributes;
    }

    /**
     *
     * @param string $name            
     */
    public function setTitle ($name)
    {
        $this->title = (string) $name;
    }

    /**
     *
     * @return string
     */
    public function getTitle ()
    {
        return $this->title;
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

    public function addShowOnValue (Column\AbstractColumn $col, $value = null, $operator = Filter::EQUAL)
    {
        $this->showOnValues[] = array(
            'column' => $col,
            'value' => $value,
            'operator' => $operator
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
            foreach ($this->getShowOnValues() as $rule) {
                $value = '';
                if (isset($row[$rule['column']->getUniqueId()])) {
                    $value = $row[$rule['column']->getUniqueId()];
                }
                
                switch ($rule['operator']) {
                    case Filter::EQUAL:
                        if ($rule['value'] == $value) {
                            return true;
                        }
                        break;
                    
                    case Filter::NOT_EQUAL:
                        if ($rule['value'] != $value) {
                            return true;
                        }
                        break;
                    
                    default:
                        throw new \Exception('currently not implemented filter type: "' . $rule['operator'] . '"');
                        break;
                }
            }
        } else {
            return true;
        }
        
        return false;
    }

    /**
     *
     * @return
     *
     *
     *
     *
     */
    abstract public function toHtml ();
}
