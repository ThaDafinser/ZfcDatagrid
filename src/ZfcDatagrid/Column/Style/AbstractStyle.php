<?php
namespace ZfcDatagrid\Column\Style;

use ZfcDatagrid\Column\AbstractColumn;
use ZfcDatagrid\Filter;

abstract class AbstractStyle
{
    protected $byValueOperator = 'OR';

    /**
     *
     * @var array
     */
    private $byValues = array();

    /**
     * Display the values with AND or OR (if multiple showOnValues are defined)
     *
     * @param string $operator
     */
    public function setByValueOperator($operator = 'OR')
    {
        if ($operator != 'AND' && $operator != 'OR') {
            throw new \InvalidArgumentException('not allowed operator: "'.$operator.'" (AND / OR is allowed)');
        }

        $this->byValueOperator = (string) $operator;
    }

    /**
     * Get the show on value operator, e.g.
     * OR, AND
     *
     * @return string
     */
    public function getByValueOperator()
    {
        return $this->byValueOperator;
    }

    /**
     * Set the style value based and not general
     *
     * @param AbstractColumn $column
     * @param mixed          $value
     * @param string         $operator
     */
    public function addByValue(AbstractColumn $column, $value, $operator = Filter::EQUAL)
    {
        $this->byValues[] = array(
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
        );
    }

    /**
     * Set the style value based and not general
     *
     * @param AbstractColumn $column
     * @param mixed          $value
     * @param string         $operator
     */
    public function setByValue(AbstractColumn $column, $value, $operator = Filter::EQUAL)
    {
        trigger_error(__CLASS__.'::setByValue() is deprecated, please use "addByValue" instead', E_USER_DEPRECATED);

        $this->addByValue($column, $value, $operator);
    }

    /**
     *
     * @return array
     */
    public function getByValues()
    {
        return $this->byValues;
    }

    /**
     *
     * @return boolean
     */
    public function hasByValues()
    {
        if (count($this->byValues) > 0) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param  array   $row
     * @return boolean
     */
    public function isApply(array $row)
    {
        if ($this->hasByValues() === false) {
            return true;
        }

        $isApply = false;
        foreach ($this->getByValues() as $rule) {
            $value = '';
            if (isset($row[$rule['column']->getUniqueId()])) {
                $value = $row[$rule['column']->getUniqueId()];
            }

            $isApplyMatch = Filter::isApply($value, $rule['value'], $rule['operator']);
            if ($this->getByValueOperator() == 'OR' && true === $isApplyMatch) {
                // For OR one match is enough
                return true;
            } elseif ($this->getByValueOperator() == 'AND' && false === $isApplyMatch) {
                return false;
            } else {
                $isApply = $isApplyMatch;
            }
        }

        return $isApply;
    }
}
