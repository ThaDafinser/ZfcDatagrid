<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Filter;
use ZfcDatagrid\Column;
use Zend\Paginator\Adapter\DbSelect as PaginatorAdapter;
use Zend\Db\Sql;

class ZendSelect extends AbstractDataSource
{

    /**
     *
     * @var Sql\Select
     */
    private $select;

    /**
     *
     * @var \Zend\Db\Sql\Sql
     */
    private $sqlObject;


    /**
     * The data result
     *
     * @var PaginatorAdapter
     */
    private $paginatorAdapter;

    /**
     * Data source
     *
     * @param mixed $data            
     */
    public function __construct ($data)
    {
        if ($data instanceof Sql\Select) {
            $this->select = $data;
        } else {
            throw new \Exception("Unknown data input..." . get_class($data));
        }
    }

    public function setAdapter ($adapterOrSqlObject)
    {
        if ($adapterOrSqlObject instanceof \Zend\Db\Sql\Sql) {
            $this->sqlObject = $adapterOrSqlObject;
        } elseif ($adapterOrSqlObject instanceof \Zend\Db\Adapter\Adapter) {
            $this->sqlObject = new \Zend\Db\Sql\Sql($adapterOrSqlObject);
        } else {
            throw new \Exception("Unknown $adapterOrSqlObject..." . get_class($adapterOrSqlObject));
        }
    }

    public function execute ()
    {
        if ($this->sqlObject === null || ! $this->sqlObject instanceof \Zend\Db\Sql\Sql) {
            throw new \Exception('setAdapter() must be called first!');
        }
        
        $select = $this->select;
        
        /**
         * Step 1) Apply needed columns
         */
        $selectColumns = array();
        foreach ($this->columns as $column) {
            if ($column instanceof Column\Standard && ! $column->hasDataPopulation()) {
                // $colString = $column->getSelectPart1();
                // if ($column->getSelectPart2() != '') {
                $colString = $column->getSelectPart2();
                // }
                // $colString .= ' as ' . $column->getUniqueId();
                
                $selectColumns[$column->getUniqueId()] = $colString;
            }
        }
        $select->columns($selectColumns);
        
        /**
         * Step 2) Apply sorting
         */
        if (count($this->sortConditions) > 0) {
            // Minimum one sort condition given -> so reset the default orderBy
            $select->reset(Sql\Select::ORDER);
            
            foreach ($this->sortConditions as $sortCondition) {
                $column = $sortCondition['column'];
                $select->order($column->getUniqueId() . ' ' . $sortCondition['sortDirection']);
            }
        }
        
        $adapter = $this->sqlObject->getAdapter();
        $qi = function  ($name) use( $adapter)
        {
            return $adapter->getPlatform()->quoteIdentifier($name);
        };
        $qv = function  ($value) use( $adapter)
        {
            return $adapter->getPlatform()->quoteValue($value);
        };
        $fp = function  ($name) use( $adapter)
        {
            return $adapter->getDriver()->formatParameterName($name);
        };
        
        /**
         * Step 3) Apply filters
         */
        foreach ($this->filters as $filter) {
            /* @var $filter \ZfcDatagrid\Filter */
            if ($filter->isColumnFilter() === true) {
                $values = $filter->getValue();
                
                $column = $filter->getColumn();
                $colString = $column->getUniqueId();
                
                switch ($filter->getOperator()) {
                    
                    case Filter::LIKE:
                        $select->where->like($colString, '%' . $values[0] . '%');
                        break;
                    
                    case Filter::LIKE_LEFT:
                        $select->where->like($colString, '%' . $values[0]);
                        break;
                    
                    case Filter::LIKE_RIGHT:
                        $select->where->like($colString, $values[0] . '%');
                        break;
                    
                    case Filter::NOT_LIKE:
                        $select->where->literal($qi($colString) . ' NOT LIKE ?', array(
                            '%' . $values[0] . '%'
                        ));
                        break;
                    
                    case Filter::NOT_LIKE_LEFT:
                        $select->where->literal($qi($colString) . 'NOT LIKE ?', array(
                            '%' . $values[0]
                        ));
                        break;
                    
                    case Filter::NOT_LIKE_RIGHT:
                        $select->where->literal($qi($colString) . 'NOT LIKE ?', array(
                            $values[0] . '%'
                        ));
                        break;
                    
                    case Filter::EQUAL:
                        $select->where->equalTo($colString, $values[0]);
                        break;
                    
                    case Filter::NOT_EQUAL:
                        $select->where->notEqualTo($colString, $values[0]);
                        break;
                    
                    case Filter::GREATER_EQUAL:
                        $select->where->greaterThanOrEqualTo($colString, $values[0]);
                        break;
                    
                    case Filter::GREATER:
                        $select->where->greaterThan($colString, $values[0]);
                        break;
                    
                    case Filter::LESS_EQUAL:
                        $select->where->lessThanOrEqualTo($colString, $values[0]);
                        break;
                    
                    case Filter::LESS:
                        $select->where->lessThan($colString, $values[0]);
                        break;
                    
                    case Filter::BETWEEN:
                        $select->where->between($colString, $values[0], $values[1]);
                        break;
                    
                    default:
                        throw new \Exception('This operator is currently not supported: ' . $filter->getOperator());
                        break;
                }
            }
        }
        
        /**
         * Step 4) Pagination
         */
        $this->paginatorAdapter = new PaginatorAdapter($select, $this->sqlObject);
    }

    /**
     *
     * @return PaginatorAdapter
     */
    public function getPaginatorAdapter ()
    {
        return $this->paginatorAdapter;
    }
}
