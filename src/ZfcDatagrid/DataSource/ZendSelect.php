<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Filter;
use ZfcDatagrid\Column;
use Zend\Paginator\Adapter\DbSelect as PaginatorAdapter;
use Zend\Db\Sql;

class ZendSelect implements DataSourceInterface
{

    /**
     *
     * @var Sql\Select
     */
    private $select;

    /**
     *
     * @var unknown
     */
    private $adapterOrSqlObject;

    private $columns = array();

    private $sortConditions = array();

    private $filters = array();

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
        if ($adapterOrSqlObject instanceof \Zend\Db\Adapter\Adapter || $adapterOrSqlObject instanceof \Zend\Db\Sql\Sql) {
            $this->adapterOrSqlObject = $adapterOrSqlObject;
        } else {
            throw new \Exception("Unknown $adapterOrSqlObject..." . get_class($adapterOrSqlObject));
        }
    }

    public function setColumns (array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Set sort conditions
     *
     * @param Column\AbstractColumn $column            
     * @param string $sortDirection            
     */
    public function addSortCondition (Column\AbstractColumn $column, $sortDirection = 'ASC')
    {
        $this->sortConditions[] = array(
            'column' => $column,
            'sortDirection' => $sortDirection
        );
    }

    /**
     * Add a filter rule
     *
     * @param Filter $filter            
     */
    public function addFilter (Filter $filter)
    {
        $this->filters[] = $filter;
    }

    public function execute ()
    {
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
                        $select->where->like($colString, $values[0]);
                        break;
                    
                    case Filter::LIKE_LEFT:
                        break;
                    
                    case Filter::LIKE_RIGHT:
                        break;
                    
                    case Filter::NOT_LIKE:
                        break;
                    
                    case Filter::NOT_LIKE_LEFT:
                        break;
                    
                    case Filter::NOT_LIKE_RIGHT:
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
        if ($this->adapterOrSqlObject === null) {
            throw new \Exception('Missing $adapterOrSqlObject...');
        }
        $this->paginatorAdapter = new PaginatorAdapter($select, $this->adapterOrSqlObject);
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
