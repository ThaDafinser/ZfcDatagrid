<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Filter;
use ZfcDatagrid\Datasource\Doctrine2Paginator as PaginatorAdapter;
use ZfcDatagrid\Column;
use Doctrine\ORM;
use Doctrine\ORM\Query\Expr;

class Doctrine2 implements DataSourceInterface
{

    /**
     *
     * @var ORM\QueryBuilder
     */
    private $queryBuilder;

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
        if ($data instanceof ORM\QueryBuilder) {
            $this->queryBuilder = $data;
        } else {
            throw new \Exception("Unknown data input..." . get_class($data));
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
        $queryBuilder = $this->queryBuilder;
        
        /**
         * Step 1) Apply needed columns
         */
        $selectColumns = array();
        foreach ($this->columns as $column) {
            if ($column instanceof Column\Standard && ! $column->hasDataPopulation()) {
                $colString = $column->getSelectPart1();
                if ($column->getSelectPart2() != '') {
                    $colString .= '.' . $column->getSelectPart2();
                }
                $colString .= ' ' . $column->getUniqueId();
                
                $selectColumns[] = $colString;
            }
        }
        $queryBuilder->resetDQLPart('select');
        $queryBuilder->select($selectColumns);
        
        /**
         * Step 2) Apply sorting
         */
        if (count($this->sortConditions) > 0) {
            // Minimum one sort condition given -> so reset the default orderBy
            $queryBuilder->resetDQLPart('orderBy');
            
            foreach ($this->sortConditions as $sortCondition) {
                $column = $sortCondition['column'];
                $queryBuilder->add('orderBy', new Expr\OrderBy($column->getUniqueId(), $sortCondition['sortDirection']), true);
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
                $colString = $column->getSelectPart1();
                if ($column->getSelectPart2() != '') {
                    $colString .= '.' . $column->getSelectPart2();
                }
                
                switch ($filter->getOperator()) {
                    
                    case Filter::LIKE:
                        $queryBuilder->expr()->like($colString, $queryBuilder->expr()->literal('%'. addcslashes($values[0], '_%') .'%') );
                        break;
                    
                    case Filter::LIKE_LEFT:
                        $queryBuilder->expr()->like($colString, $queryBuilder->expr()->literal('%'. addcslashes($values[0], '_%')) );
                        break;
                    
                    case Filter::LIKE_RIGHT:
                        $queryBuilder->expr()->like($colString, $queryBuilder->expr()->literal(addcslashes($values[0], '_%') .'%') );
                        break;
                    
                    case Filter::NOT_LIKE:
                        $queryBuilder->expr()->literal($colString. 'NOT LIKE \'%'.$values[0].'%\'');
                        break;
                    
                    case Filter::NOT_LIKE_LEFT:
                        $queryBuilder->expr()->literal($colString. 'NOT LIKE \'%'.$values[0].'\'');
                        break;
                    
                    case Filter::NOT_LIKE_RIGHT:
                        $queryBuilder->expr()->literal($colString. 'NOT LIKE \''.$values[0].'%\'');
                        break;
                    
                    case Filter::EQUAL:
                        $queryBuilder->expr()->eq($colString, $values[0]);
                        break;
                    
                    case Filter::NOT_EQUAL:
                        $queryBuilder->expr()->neq($colString, $values[0]);
                        break;
                    
                    case Filter::GREATER_EQUAL:
                        $queryBuilder->expr()->gte($colString, $values[0]);
                        break;
                    
                    case Filter::GREATER:
                        $queryBuilder->expr()->gt($colString, $values[0]);
                        break;
                    
                    case Filter::LESS_EQUAL:
                        $queryBuilder->expr()->lte($colString, $values[0]);
                        break;
                    
                    case Filter::LESS:
                        $queryBuilder->expr()->lt($colString, $values[0]);
                        break;
                    
                    case Filter::BETWEEN:
                        $queryBuilder->expr()->between($colString, $values[0], $values[1]);
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
        // echo $queryBuilder->getQuery()->getSQL();
        // exit();
        $this->paginatorAdapter = new PaginatorAdapter($queryBuilder);
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
