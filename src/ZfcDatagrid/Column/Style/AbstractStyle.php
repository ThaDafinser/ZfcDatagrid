<?php

namespace ZfcDatagrid\Column\Style;

use ZfcDatagrid\Column\AbstractColumn;
use ZfcDatagrid\Filter;

abstract class AbstractStyle
{
    protected $byValueOperator = 'OR';

    /**
     * @var array
     */
    private $byValues = [];

    /**
     * Display the values with AND or OR (if multiple showOnValues are defined).
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
     * OR, AND.
     *
     * @return string
     */
    public function getByValueOperator()
    {
        return $this->byValueOperator;
    }

    /**
     * Set the style value based and not general.
     *
     * @param AbstractColumn $column
     * @param mixed          $value
     * @param string         $operator
     */
    public function addByValue(AbstractColumn $column, $value, $operator = Filter::EQUAL)
    {
        $this->byValues[] = [
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
        ];
    }

    /**
     * @return array
     */
    public function getByValues()
    {
        return $this->byValues;
    }

    /**
     * @return bool
     */
    public function hasByValues()
    {
        if (count($this->byValues) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param array $row
     *
     * @return bool
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

            if ($rule['value'] instanceof AbstractColumn) {
                if (isset($row[$rule['value']->getUniqueId()])) {
                    $ruleValue = $row[$rule['value']->getUniqueId()];
                } else {
                    $ruleValue = '';
                }
            } else {
                $ruleValue = $rule['value'];
            }

            $isApplyMatch = Filter::isApply($value, $ruleValue, $rule['operator']);
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
