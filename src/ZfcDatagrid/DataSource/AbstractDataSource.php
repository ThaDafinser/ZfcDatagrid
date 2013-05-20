<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;
use Zend\Paginator\Adapter\AdapterInterface as PaginatorAdapterInterface;

abstract class AbstractDataSource implements DataSourceInterface
{

    /**
     *
     * @var array
     */
    protected $columns = array();

    /**
     *
     * @var array
     */
    protected $sortConditions = array();

    /**
     *
     * @var array
     */
    protected $filters = array();

    /**
     * The data result
     *
     * @var \Zend\Paginator\Adapter\AdapterInterface
     */
    private $paginatorAdapter;

    /**
     * Set the columns
     *
     * @param array $columns            
     */
    public function setColumns (array $columns)
    {
        $this->columns = $columns;
    }

    /**
     *
     * @return array
     */
    public function getColumns ()
    {
        return $this->columns;
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
     *
     * @return array
     */
    public function getSortConditions ()
    {
        return $this->sortConditions;
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

    /**
     *
     * @return array
     */
    public function getFilters ()
    {
        return $this->filters;
    }

    public function setPaginatorAdapter(PaginatorAdapterInterface $paginator){
        $this->paginatorAdapter = $paginator;
    }
    
    /**
     *
     * @return \Zend\Paginator\Adapter\AdapterInterface
     */
    public function getPaginatorAdapter ()
    {
        return $this->paginatorAdapter;
    }
}