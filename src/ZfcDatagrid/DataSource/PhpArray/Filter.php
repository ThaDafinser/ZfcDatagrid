<?php
namespace ZfcDatagrid\Datasource\PhpArray;

class Filter
{

    private $columnIndex;

    private $valueToFilter;

    public function __construct ($columnIndex, $valueToFilter)
    {
        $this->columnIndex = $columnIndex;
        $this->valueToFilter = $valueToFilter;
    }

    public function isLike ($value)
    {
        if (stripos($value[$this->columnIndex], $this->valueToFilter[0]) !== false) {
            return true;
        }
        
        return false;
    }

    /**
     *
     * @todo is currently LIKE "isLike()"
     */
    public function isLikeLeft ($value)
    {
        if (stripos($value[$this->columnIndex], $this->valueToFilter[0]) !== false) {
            return true;
        }
        
        return false;
    }

    /**
     *
     * @todo is currently LIKE "isLike()"
     */
    public function isLikeRight ($value)
    {
        if (stripos($value[$this->columnIndex], $this->valueToFilter[0]) !== false) {
            return true;
        }
        
        return false;
    }

    public function isNotLike ($value)
    {
        if (stripos($value[$this->columnIndex], $this->valueToFilter[0]) === false) {
            return true;
        }
        
        return false;
    }

    /**
     *
     * @todo is currently LIKE "isNotLike()"
     */
    public function isNotLikeLeft ($value)
    {
        if (stripos($value[$this->columnIndex], $this->valueToFilter[0]) === false) {
            return true;
        }
        
        return false;
    }

    /**
     *
     * @todo is currently LIKE "isNotLike()"
     */
    public function isNotLikeRight ($value)
    {
        if (stripos($value[$this->columnIndex], $this->valueToFilter[0]) === false) {
            return true;
        }
        
        return false;
    }

    public function isEqual ($value)
    {
        return $value[$this->columnIndex] == $this->valueToFilter[0];
    }

    public function isNotEqual ($value)
    {
        return $value[$this->columnIndex] != $this->valueToFilter[0];
    }

    public function isGreaterEqual ($value)
    {
        return $value[$this->columnIndex] >= $this->valueToFilter[0];
    }

    public function isGreater ($value)
    {
        return $value[$this->columnIndex] > $this->valueToFilter[0];
    }

    public function isLessEqual ($value)
    {
        return $value[$this->columnIndex] <= $this->valueToFilter[0];
    }

    public function isLess ($value)
    {
        return $value[$this->columnIndex] < $this->valueToFilter[0];
    }

    public function isBetween ($value)
    {
        if ($value[$this->columnIndex] >= $this->valueToFilter[0] && $value[$this->columnIndex] <= $this->valueToFilter[1]) {
            return true;
        }
        
        return false;
    }
}
