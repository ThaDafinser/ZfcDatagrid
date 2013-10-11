<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Filter;
use ZfcDatagrid\DataSource\Doctrine2\Paginator as PaginatorAdapter;
use ZfcDatagrid\Column;
use Doctrine\ORM;
use Doctrine\ORM\Query\Expr;

class Doctrine2 extends AbstractDataSource
{

    /**
     *
     * @var ORM\QueryBuilder
     */
    private $queryBuilder;

    /**
     * Data source
     *
     * @param mixed $data            
     */
    public function __construct($data)
    {
        if ($data instanceof ORM\QueryBuilder) {
            $this->queryBuilder = $data;
        } else {
            $return = $data;
            if (is_object($data)) {
                $return = get_class($return);
            }
            throw new \InvalidArgumentException("Unknown data input..." . $return);
        }
    }

    /**
     *
     * @return ORM\QueryBuilder
     */
    public function getData()
    {
        return $this->queryBuilder;
    }

    public function execute()
    {
        $queryBuilder = $this->getData();
        
        /**
         * Step 1) Apply needed columns
         */
        $selectColumns = array();
        foreach ($this->getColumns() as $column) {
            if ($column instanceof Column\Select && ! $column->hasDataPopulation()) {
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
        if (count($this->getSortConditions()) > 0) {
            // Minimum one sort condition given -> so reset the default orderBy
            $queryBuilder->resetDQLPart('orderBy');
            
            foreach ($this->getSortConditions() as $sortCondition) {
                $column = $sortCondition['column'];
                $queryBuilder->add('orderBy', new Expr\OrderBy($column->getUniqueId(), $sortCondition['sortDirection']), true);
            }
        }
        
        /**
         * Step 3) Apply filters
         */
        $filterColumn = new Doctrine2\Filter($queryBuilder);
        foreach ($this->getFilters() as $filter) {
            if ($filter->isColumnFilter() === true) {
                $filterColumn->applyFilter($filter);
            }
        }
        
        /**
         * Step 4) Pagination
         */
        $this->setPaginatorAdapter(new PaginatorAdapter($queryBuilder));
    }
}
