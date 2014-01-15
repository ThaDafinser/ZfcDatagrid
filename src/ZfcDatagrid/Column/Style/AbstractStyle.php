<?php
namespace ZfcDatagrid\Column\Style;

use ZfcDatagrid\Column\AbstractColumn;
use ZfcDatagrid\Filter;

abstract class AbstractStyle
{

    /**
     *
     * @var array
     */
    private $byValues = array();

    /**
     * Set the style value based and not general
     *
     * @param AbstractColumn $column            
     * @param mixed $value            
     * @param string $operator            
     */
    public function setByValue(AbstractColumn $column, $value, $operator = Filter::EQUAL)
    {
        $this->byValues[] = array(
            'column' => $column,
            'value' => $value,
            'operator' => $operator
        );
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
    public function isForAll()
    {
        if (count($this->byValues) === 0) {
            return true;
        }
        
        return false;
    }

    /**
     *
     * @param array $row            
     * @return boolean
     */
    public function isApply($row)
    {
        if ($this->isForAll() === true) {
            return true;
        } else {
            
            foreach ($this->getByValues() as $rule) {
                $value = '';
                if (isset($row[$rule['column']->getUniqueId()])) {
                    $value = $row[$rule['column']->getUniqueId()];
                }
                
                return Filter::isApply($value, $rule['value'], $rule['operator']);
            }
        }
        
        return false;
    }
}
