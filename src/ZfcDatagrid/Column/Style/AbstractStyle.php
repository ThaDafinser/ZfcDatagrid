<?php
namespace ZfcDatagrid\Column\Style;

use ZfcDatagrid\Column\AbstractColumn;
abstract class AbstractStyle implements StyleInterface
{

    private $byValues = array();

    public function setByValue (AbstractColumn $column, $value, $operator = 'equal')
    {
        $this->byValues[] = array(
            'column' => $column,
            'value' => $value,
            'operator' => $operator
        );
    }

    public function getByValues ()
    {
        return $this->byValues;
    }

    public function isForAll ()
    {
        if (count($this->byValues) === 0) {
            return true;
        }
        
        return false;
    }

    public function isApply ($row)
    {
        if ($this->isForAll() === true) {
            return true;
        } else {

            foreach ($this->getByValues() as $rule) {
                $value = '';
                if(isset($row[$rule['column']->getUniqueId()]))
                    $value = $row[$rule['column']->getUniqueId()];
                    
                if ($rule['operator'] === 'equal' && $rule['value'] == $value) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
