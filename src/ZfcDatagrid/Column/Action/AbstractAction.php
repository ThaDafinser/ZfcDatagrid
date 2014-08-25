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
     * @return AbstractAction
     */
    public function setLink($href)
    {
        $this->setAttribute('href', $href);

        return $this;
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
     * @param  array  $row
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
     * @param  AbstractColumn $col
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
     * @return AbstractAction
     */
    public function setAttribute($name, $value)
    {
        $this->htmlAttributes[$name] = (string) $value;

        return $this;
    }

    /**
     * Get a HTML attribute
     *
     * @param  string $name
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
     * Removes an HTML attribute
     *
     * @param string $name
     * @return AbstractAction
     */
    public function removeAttribute($name)
    {
        if (isset($this->htmlAttributes[$name])) {
            unset($this->htmlAttributes[$name]);
        }

        return $this;
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
     * @param  array  $row
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
     * @return AbstractAction
     */
    public function setTitle($name)
    {
        $this->setAttribute('title', $name);

        return $this;
    }

    /**
     * Get the title attribute
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    /**
     * Add a css class
     *
     * @param string $className
     * @return AbstractAction
     */
    public function addClass($className)
    {
        $attr = $this->getAttribute('class');
        if ($attr != '')
            $attr .= ' ';
        $attr .= (string) $className;

        $this->setAttribute('class', $attr);

        return $this;
    }

    /**
     * Display the values with AND or OR (if multiple showOnValues are defined)
     *
     * @param string $operator
     * @return AbstractAction
     */
    public function setShowOnValueOperator($operator = 'OR')
    {
        if ($operator != 'AND' && $operator != 'OR') {
            throw new \InvalidArgumentException('not allowed operator: "' . $operator . '" (AND / OR is allowed)');
        }

        $this->showOnValueOperator = (string) $operator;

        return $this;
    }

    /**
     * Get the show on value operator, e.g.
     * OR, AND
     *
     * @return string
     */
    public function getShowOnValueOperator()
    {
        return $this->showOnValueOperator;
    }

    /**
     * Show this action only on the values defined
     *
     * @param        $col
     * @param null   $value
     * @param string $comparison
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addShowOnValue($col, $value = null, $comparison = Filter::EQUAL)
    {
        if (is_array($col)) {
            $args = func_get_args();
            if (count($args) > 2 && empty($value)) {
                throw new \InvalidArgumentException(
                    'The second argument cannot be empty and must be a value operator (OR, AND)'
                );
            }

            if (!isset($col['columns']) || !isset($col['values']) || !isset($col['comparison'])) {
                throw new \InvalidArgumentException(
                    'The first argument must contains "columns", "values" and "comparison" keys'
                );
            }
            if (count($col['columns']) !== count($col['values'])) {
                throw new \InvalidArgumentException('You must supply the same ammount of values and columns');
            }

            if (is_array($col['comparison']) && count($col['columns']) !== count($col['comparison'])) {
                throw new \InvalidArgumentException(
                    'If $comparison argument is and array, you must supply one comparison for each column'
                );
            }

            $rules = array();
            foreach ($col['columns'] as $key => $column) {
                if (!$column instanceof Column\AbstractColumn) {
                    throw new \InvalidArgumentException(
                        'If first argument is an array it must contains a collection of elements implementing '.
                        'AbstractColumn, (' . gettype($column) === 'object'
                            ? get_class($column) . ')'
                            : gettype($column) .
                            ' found)'
                    );
                }

                $rules[] = array(
                    'column' => $column,
                    'value' => $col['values'][$key],
                    'comparison' => is_array($col['comparison']) ? $col['comparison'][$key] : $col['comparison'],
                );
            }
            $this->showOnValues[] = array(
                'column' => $rules,
                'comparison' => $value
            );
        } else {
            $this->showOnValues[] = array(
                'column' => $col,
                'value' => $value,
                'comparison' => $comparison
            );
        }

        return $this;
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
     * @param  array   $row
     * @return boolean
     */
    public function isDisplayed(array $row)
    {
        if ($this->hasShowOnValues() === false) {
            return true;
        }

        $isDisplayed = false;
        foreach ($this->getShowOnValues() as $rule) {
            if (is_array($rule['column'])) {
                $isDisplayedMatch = $this->applyFilters($row, $rule['column'], null, null, $rule['comparison']);
            } else {
                $isDisplayedMatch = $this->applyFilters($row, $rule['column'], $rule['value'], $rule['comparison']);
            }

            if ($this->getShowOnValueOperator() == 'OR' && $isDisplayedMatch === true) {
                return true;
            } elseif ($this->getShowOnValueOperator() == 'AND' && $isDisplayedMatch === false) {
                return false;
            } else {
                $isDisplayed = $isDisplayedMatch;
            }
        }

        return $isDisplayed;
    }

    /**
     * @param array                         $row
     * @param Column\AbstractColumn|array   $column
     * @param null|string                   $valueCompare
     * @param null|string                   $comparison
     * @param null|string                   $showOnValueOperator
     *
     * @return bool
     */
    protected function applyFilters(
        $row,
        $column,
        $valueCompare = null,
        $comparison = null,
        $showOnValueOperator = null
    ) {
        if ($showOnValueOperator === null) {
            $showOnValueOperator = $this->getShowOnValueOperator();
        }

        $value = '';
        if (is_array($column)) {
            $isDisplayedMatch = false;
            foreach ($column as $rule) {
                $isDisplayedMatch = $this->applyFilters(
                    $row,
                    $rule['column'],
                    $rule['value'],
                    $rule['comparison'],
                    $showOnValueOperator
                );
                if ($rule['comparison'] == 'OR' && $isDisplayedMatch === true) {
                    return true;
                } elseif ($rule['comparison'] && 'AND' && $isDisplayedMatch === false) {
                    return false;
                }
            }

            return $isDisplayedMatch;
        } else {
            if (isset($row[$column->getUniqueId()])) {
                $value = $row[$column->getUniqueId()];
            }

            return Filter::isApply($value, $valueCompare, $comparison);
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
     * @param  array  $row
     * @return string
     */
    public function toHtml(array $row)
    {
        return '<a ' . $this->getAttributesString($row) . '>' . $this->getHtmlType() . '</a>';
    }
}
