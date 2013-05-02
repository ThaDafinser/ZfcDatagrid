<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Datasource\Doctrine2Paginator as PaginatorAdapter;
use ZfcDatagrid\Column\AbstractColumn;
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
     * @param AbstractColumn $column            
     * @param string $sortDirection            
     */
    public function addSortCondition (AbstractColumn $column, $sortDirection = 'ASC')
    {
        $this->sortConditions[] = array(
            'column' => $column,
            'sortDirection' => $sortDirection
        );
    }

    /**
     * Add a filter rule
     */
    public function addFilter ()
    {}

    public function execute ()
    {
        $queryBuilder = $this->queryBuilder;
        
        /**
         * Step 1) Apply needed columns
         */
        $selectColumns = array();
        foreach ($this->columns as $column) {
            if (! $column->hasDataPopulation()) {
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
        
        /**
         * Step 4) Pagination
         */
//         echo $queryBuilder->getQuery()->getSQL();
//         exit();
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
