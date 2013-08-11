<?php
namespace ZfcDatagrid\DataSource\PhpArray;

use ZfcDatagrid\Filter as DatagridFilter;

class Filter
{

    /**
     *
     * @var \ZfcDatagrid\Filter
     */
    private $filter;

    /**
     *
     * @param string $uniqueId            
     * @param array $valuesToFilter            
     */
    public function __construct(\ZfcDatagrid\Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * 
     * @return \ZfcDatagrid\Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    private function getRowValue($row)
    {
        $rowValue = $row[$this->getFilter()
            ->getColumn()
            ->getUniqueId()];
        
        if (! is_array($rowValue)) {
            $rowValue = (string) $rowValue;
        }
        
        return $rowValue;
    }

    public function applyFilter($row)
    {
        $return = false;
        
        $filters = $this->getFilter()->getValues();
        foreach ($filters as $filter) {
            $value = $this->getRowValue($row);
            
            switch ($this->getFilter()->getOperator()) {
                
                case DatagridFilter::LIKE:
                    if (stripos($value, $filter) !== false) {
                        $return = true;
                    }
                    break;
                
                case DatagridFilter::LIKE_LEFT:
                    $length = strlen($filter);
                    $start = 0 - $length;
                    $searchedValue = substr($value, $start, $length);
                    if (stripos($searchedValue, $filter) !== false) {
                        $return = true;
                    }
                    break;
                
                case DatagridFilter::LIKE_RIGHT:
                    $length = strlen($filter);
                    $searchedValue = substr($value, 0, $length);
                    if (stripos($searchedValue, $filter) !== false) {
                        $return = true;
                    }
                    break;
                
                case DatagridFilter::NOT_LIKE:
                    if (stripos($value, $filter) === false) {
                        $return = true;
                    } else {
                        // For not LIKE only one valid has to be matched, that it gets false
                        $return = false;
                        break 2;
                    }
                    break;
                
                case DatagridFilter::NOT_LIKE_LEFT:
                    $length = strlen($filter);
                    $start = 0 - $length;
                    $searchedValue = substr($value, $start, $length);
                    if (stripos($searchedValue, $filter) === false) {
                        $return = true;
                    } else {
                        // For not LIKE only one valid has to be matched, that it gets false
                        $return = false;
                        break 2;
                    }
                    break;
                
                case DatagridFilter::NOT_LIKE_RIGHT:
                    $length = strlen($filter);
                    $searchedValue = substr($value, 0, $length);
                    if (stripos($searchedValue, $filter) === false) {
                        $return = true;
                    } else {
                        // For not LIKE only one valid has to be matched, that it gets false
                        $return = false;
                        break 2;
                    }
                    break;
                
                case DatagridFilter::EQUAL:
                case DatagridFilter::IN:
                    if ($value == $filter) {
                        $return = true;
                    }
                    break;
                
                case DatagridFilter::NOT_EQUAL:
                case DatagridFilter::NOT_IN:
                    if ($value != $filter) {
                        $return = true;
                    } else {
                        $return = false;
                        break 2;
                    }
                    break;
                
                case DatagridFilter::GREATER_EQUAL:
                    if ($value >= $filter) {
                        $return = true;
                    }
                    break;
                
                case DatagridFilter::GREATER:
                    if ($value > $filter) {
                        $return = true;
                    }
                    break;
                
                case DatagridFilter::LESS_EQUAL:
                    if ($value <= $filter) {
                        $return = true;
                    }
                    break;
                
                case DatagridFilter::LESS:
                    if ($value < $filter) {
                        $return = true;
                    }
                    break;
                
                case DatagridFilter::BETWEEN:
                    $min = min($filters);
                    $max = max($filters);
                    if ($value >= $min && $value <= $max) {
                        $return = true;
                        break 2;
                    }
                    break;
                
                default:
                    throw new \Exception('This filter mode is not supported for PhpArray source: "' . $this->getFilter()->getOperator() . '"');
                    break;
            }
        }
        
        return $return;
    }
}
