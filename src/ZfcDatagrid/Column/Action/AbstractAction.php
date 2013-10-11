<?php
namespace ZfcDatagrid\Column\Action;

use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\AbstractColumn;

abstract class AbstractAction
{

    const ROW_ID_PLACEHOLDER = ':rowId:';

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn[]
     */
    protected $linkColumnPlaceholders = array();

    /**
     *
     * @var array
     */
    protected $htmlAttributes = array();

    protected $showOnValueOperator = 'OR';

    /**
     *
     * @var array
     */
    protected $showOnValues = array();

    public function __construct()
    {
        $this->setLink('#');
    }

    /**
     * Set the link
     *
     * @param string $href            
     */
    public function setLink($href)
    {
        $this->setAttribute('href', $href);
    }

    /**
     *
     * @return string
     */
    public function getLink()
    {
        return $this->getAttribute('href');
    }

    /**
     * This is needed public for rowClickAction...
     *
     * @param array $row            
     * @return string
     */
    public function getLinkReplaced(array $row)
    {
        $link = $this->getLink();
        
        // Replace placeholders
        if (strpos($this->getLink(), self::ROW_ID_PLACEHOLDER) !== false) {
            $link = str_replace(self::ROW_ID_PLACEHOLDER, $row['idConcated'], $link);
        }
        
        foreach ($this->getLinkColumnPlaceholders() as $col) {
            $link = str_replace(':' . $col->getUniqueId() . ':', $row[$col->getUniqueId()], $link);
        }
        
        return $link;
    }

    /**
     * Get the column row value placeholder
     * $action->setLink('/myLink/something/id/'.$action->getRowIdPlaceholder().'/something/'.$action->getColumnRowPlaceholder($myCol));
     *
     * @param AbstractColumn $col            
     * @return string
     */
    public function getColumnValuePlaceholder(AbstractColumn $col)
    {
        $this->linkColumnPlaceholders[] = $col;
        
        return ':' . $col->getUniqueId() . ':';
    }

    /**
     *
     * @return \ZfcDatagrid\Column\AbstractColumn[]
     */
    public function getLinkColumnPlaceholders()
    {
        return $this->linkColumnPlaceholders;
    }

    /**
     * Returns the rowId placeholder
     * Can be used e.g.
     * $action->setLink('/myLink/something/id/'.$action->getRowIdPlaceholder());
     *
     * @return string
     */
    public function getRowIdPlaceholder()
    {
        return self::ROW_ID_PLACEHOLDER;
    }

    /**
     * Set a HTML attributes
     *
     * @param string $name            
     * @param string $value            
     */
    public function setAttribute($name, $value)
    {
        $this->htmlAttributes[$name] = (string) $value;
    }

    /**
     * Get a HTML attribute
     *
     * @param string $name            
     * @return string
     */
    public function getAttribute($name)
    {
        if (isset($this->htmlAttributes[$name])) {
            return $this->htmlAttributes[$name];
        }
        
        return '';
    }

    /**
     * Get all HTML attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->htmlAttributes;
    }

    /**
     * Get the string version of the attributes
     *
     * @param array $row            
     * @return string
     */
    protected function getAttributesString(array $row)
    {
        $attributes = array();
        foreach ($this->getAttributes() as $attrKey => $attrValue) {
            if ($attrKey === 'href') {
                $attrValue = $this->getLinkReplaced($row);
            }
            $attributes[] = $attrKey . '="' . $attrValue . '"';
        }
        
        return implode(' ', $attributes);
    }

    /**
     * Set the title attribute
     *
     * @param string $name            
     */
    public function setTitle($name)
    {
        $this->setAttribute('title', $name);
    }

    /**
     * Get the title attribute
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getAttribute('title');
        return $this->title;
    }

    /**
     * Add a css class
     *
     * @param string $className            
     */
    public function addClass($className)
    {
        $attr = $this->getAttribute('class');
        if ($attr != '')
            $attr .= ' ';
        $attr .= (string) $className;
        
        $this->setAttribute('class', $attr);
    }

    /**
     * Display the values with AND or OR (if multiple showOnValues are defined)
     *
     * @param string $operator            
     */
    public function setShowOnValueOperator($operator = 'OR')
    {
        if ($operator != 'AND' && $operator != 'OR') {
            throw new \InvalidArgumentException('not allowed operator: "' . $operator . '" (AND / OR is allowed)');
        }
        
        $this->showOnValueOperator = (string) $operator;
    }

    public function getShowOnValueOperator()
    {
        return $this->showOnValueOperator;
    }

    /**
     * Show this action only on the values defined
     *
     * @param Column\AbstractColumn $col            
     * @param string $value            
     * @param string $comparison            
     */
    public function addShowOnValue(Column\AbstractColumn $col, $value = null, $comparison = Filter::EQUAL)
    {
        $this->showOnValues[] = array(
            'column' => $col,
            'value' => $value,
            'comparison' => $comparison
        );
    }

    /**
     *
     * @return array
     */
    public function getShowOnValues()
    {
        return $this->showOnValues;
    }

    /**
     *
     * @return boolean
     */
    public function hasShowOnValues()
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
    public function isDisplayed(array $row)
    {
        if ($this->hasShowOnValues() === true) {
            
            $isDisplayed = false;
            
            foreach ($this->getShowOnValues() as $rule) {
                
                $value = '';
                if (isset($row[$rule['column']->getUniqueId()])) {
                    $value = $row[$rule['column']->getUniqueId()];
                }
                
                $isDisplayedMatch = $this->isShownOnValue($value, $rule['value'], $rule['comparison']);
                
                if ($this->getShowOnValueOperator() == 'OR' && $isDisplayedMatch === true) {
                    // For OR one match is enough
                    $isDisplayed = true;
                    break;
                } else {
                    // AND
                    if ($isDisplayedMatch !== true) {
                        // one time no match is enough
                        $isDisplayed = false;
                        break;
                    } else {
                        $isDisplayed = true;
                    }
                }
            }
            
            return $isDisplayed;
        }
        
        return true;
    }

    /**
     *
     * @param mixed $currentValue            
     * @param mixed $expectedValue            
     * @param string $operator            
     * @throws \Exception
     * @return boolean
     */
    private function isShownOnValue($currentValue, $expectedValue, $operator = Filter::EQUAL)
    {
        switch ($operator) {
            case Filter::EQUAL:
                return $expectedValue == $currentValue;
                break;
            
            case Filter::NOT_EQUAL:
                return $expectedValue != $currentValue;
                break;
            
            default:
                throw new \Exception('currently not implemented filter type: "' . $operator . '"');
                break;
        }
    }

    /**
     * Get the HTML from the type
     *
     * @return string
     */
    abstract protected function getHtmlType();

    /**
     *
     * @param array $row            
     * @return string
     */
    public function toHtml(array $row)
    {
        return '<a ' . $this->getAttributesString($row) . '>' . $this->getHtmlType() . '</a>';
    }
}
