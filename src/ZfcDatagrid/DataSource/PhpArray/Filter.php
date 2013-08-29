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
     * @param \ZfcDatagrid\Filter $filter            
     */
    public function __construct(DatagridFilter $filter)
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

    /**
     *
     * @param array $row            
     * @param mixed $filterValue            
     * @param string $filterType            
     * @return mixed
     */
    private function getRowValue(array $row, $filterValue, $filterType = DatagridFilter::LIKE)
    {
        $rowValue = $row[$this->getFilter()
            ->getColumn()
            ->getUniqueId()];
        
        $rowValue = $this->getFilter()
            ->getColumn()
            ->getType()
            ->getFilterValue($rowValue);
        
        switch ($filterType) {
            
            case DatagridFilter::LIKE:
            case DatagridFilter::LIKE_LEFT:
            case DatagridFilter::LIKE_RIGHT:
            case DatagridFilter::NOT_LIKE:
            case DatagridFilter::NOT_LIKE_LEFT:
            case DatagridFilter::NOT_LIKE_RIGHT:
                $rowValue = (string) $rowValue;
                $filterValue = (string) $filterValue;
                break;
        }
        
        return array($rowValue, $filterValue);
        
        // if (! is_array($rowValue)) {
        // $rowValue = (string) $rowValue;
        // }
        
        // return $rowValue;
    }

    /**
     * Does the value get filtered?
     *
     * @param array $row            
     * @throws \Exception
     * @return boolean
     */
    public function applyFilter(array $row)
    {
        $return = false;
        
        $filters = $this->getFilter()->getValues();
        foreach ($filters as $filter) {
            list ($value, $filter) = $this->getRowValue($row, $filter, $this->getFilter()
                ->getOperator());
            
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
                    if ($value >= $filters[0] && $value <= $filters[1]) {
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
