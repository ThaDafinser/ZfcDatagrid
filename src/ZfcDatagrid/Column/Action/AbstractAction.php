<?php

namespace ZfcDatagrid\Column\Action;

use ZfcDatagrid\Column;
use ZfcDatagrid\Column\AbstractColumn;
use ZfcDatagrid\Filter;

abstract class AbstractAction
{
    const ROW_ID_PLACEHOLDER = ':rowId:';

    /**
     * @var \ZfcDatagrid\Column\AbstractColumn[]
     */
    protected $linkColumnPlaceholders = [];

    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * @var string
     */
    protected $showOnValueOperator = 'OR';

    /**
     * @var array
     */
    protected $showOnValues = [];

    public function __construct()
    {
        $this->setLink('#');
    }

    /**
     * Set the link.
     *
     * @param string $href
     */
    public function setLink($href)
    {
        $this->setAttribute('href', $href);
    }

    /**
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
     *
     * @return string
     */
    public function getLinkReplaced(array $row)
    {
        $link = $this->getLink();

        // Replace placeholders
        if (strpos($this->getLink(), self::ROW_ID_PLACEHOLDER) !== false) {
            $id = '';
            if (isset($row['idConcated'])) {
                $id = $row['idConcated'];
            }
            $link = str_replace(self::ROW_ID_PLACEHOLDER, $id, $link);
        }

        foreach ($this->getLinkColumnPlaceholders() as $col) {
            $link = str_replace(':'.$col->getUniqueId().':', $row[$col->getUniqueId()], $link);
        }

        return $link;
    }

    /**
     * Get the column row value placeholder
     * $action->setLink('/myLink/something/id/'.$action->getRowIdPlaceholder().'/something/'.$action->getColumnRowPlaceholder($myCol));.
     *
     * @param AbstractColumn $col
     *
     * @return string
     */
    public function getColumnValuePlaceholder(AbstractColumn $col)
    {
        $this->linkColumnPlaceholders[] = $col;

        return ':'.$col->getUniqueId().':';
    }

    /**
     * @return \ZfcDatagrid\Column\AbstractColumn[]
     */
    public function getLinkColumnPlaceholders()
    {
        return $this->linkColumnPlaceholders;
    }

    /**
     * Returns the rowId placeholder
     * Can be used e.g.
     * $action->setLink('/myLink/something/id/'.$action->getRowIdPlaceholder());.
     *
     * @return string
     */
    public function getRowIdPlaceholder()
    {
        return self::ROW_ID_PLACEHOLDER;
    }

    /**
     * Set a HTML attributes.
     *
     * @param string $name
     * @param string $value
     */
    public function setAttribute($name, $value)
    {
        $this->htmlAttributes[$name] = (string) $value;
    }

    /**
     * Get a HTML attribute.
     *
     * @param string $name
     *
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
     * Removes an HTML attribute.
     *
     * @param string $name
     */
    public function removeAttribute($name)
    {
        if (isset($this->htmlAttributes[$name])) {
            unset($this->htmlAttributes[$name]);
        }
    }

    /**
     * Get all HTML attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->htmlAttributes;
    }

    /**
     * Get the string version of the attributes.
     *
     * @param array $row
     *
     * @return string
     */
    protected function getAttributesString(array $row)
    {
        $attributes = [];
        foreach ($this->getAttributes() as $attrKey => $attrValue) {
            if ('href' === $attrKey) {
                $attrValue = $this->getLinkReplaced($row);
            }
            $attributes[] = $attrKey.'="'.$attrValue.'"';
        }

        return implode(' ', $attributes);
    }

    /**
     * Set the title attribute.
     *
     * @param string $name
     */
    public function setTitle($name)
    {
        $this->setAttribute('title', $name);
    }

    /**
     * Get the title attribute.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    /**
     * Add a css class.
     *
     * @param string $className
     */
    public function addClass($className)
    {
        $attr = $this->getAttribute('class');
        if ($attr != '') {
            $attr .= ' ';
        }
        $attr .= (string) $className;

        $this->setAttribute('class', $attr);
    }

    /**
     * Display the values with AND or OR (if multiple showOnValues are defined).
     *
     * @param string $operator
     */
    public function setShowOnValueOperator($operator = 'OR')
    {
        if ($operator != 'AND' && $operator != 'OR') {
            throw new \InvalidArgumentException('not allowed operator: "'.$operator.'" (AND / OR is allowed)');
        }

        $this->showOnValueOperator = (string) $operator;
    }

    /**
     * Get the show on value operator, e.g.
     * OR, AND.
     *
     * @return string
     */
    public function getShowOnValueOperator()
    {
        return $this->showOnValueOperator;
    }

    /**
     * Show this action only on the values defined.
     *
     * @param Column\AbstractColumn $col
     * @param string                $value
     * @param string                $comparison
     */
    public function addShowOnValue(Column\AbstractColumn $col, $value = null, $comparison = Filter::EQUAL)
    {
        $this->showOnValues[] = [
            'column' => $col,
            'value' => $value,
            'comparison' => $comparison,
        ];
    }

    /**
     * @return array
     */
    public function getShowOnValues()
    {
        return $this->showOnValues;
    }

    /**
     * @return bool
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
     *
     * @return bool
     */
    public function isDisplayed(array $row)
    {
        if ($this->hasShowOnValues() === false) {
            return true;
        }

        $isDisplayed = false;
        foreach ($this->getShowOnValues() as $rule) {
            $value = '';
            if (isset($row[$rule['column']->getUniqueId()])) {
                $value = $row[$rule['column']->getUniqueId()];
            }

            if ($rule['value'] instanceof AbstractColumn) {
                if (isset($row[$rule['value']->getUniqueId()])) {
                    $ruleValue = $row[$rule['value']->getUniqueId()];
                } else {
                    $ruleValue = '';
                }
            } else {
                $ruleValue = $rule['value'];
            }

            $isDisplayedMatch = Filter::isApply($value, $ruleValue, $rule['comparison']);
            if ($this->getShowOnValueOperator() == 'OR' && true === $isDisplayedMatch) {
                // For OR one match is enough
                return true;
            } elseif ($this->getShowOnValueOperator() == 'AND' && false === $isDisplayedMatch) {
                return false;
            } else {
                $isDisplayed = $isDisplayedMatch;
            }
        }

        return $isDisplayed;
    }

    /**
     * Get the HTML from the type.
     *
     * @return string
     */
    abstract protected function getHtmlType();

    /**
     * @param array $row
     *
     * @return string
     */
    public function toHtml(array $row)
    {
        return '<a '.$this->getAttributesString($row).'>'.$this->getHtmlType().'</a>';
    }
}
