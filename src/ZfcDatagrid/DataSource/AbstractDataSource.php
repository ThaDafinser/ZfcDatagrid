<?php
namespace ZfcDatagrid\DataSource;

use Zend\Paginator\Adapter\AdapterInterface as PaginatorAdapterInterface;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

abstract class AbstractDataSource implements DataSourceInterface
{
    /**
     *
     * @var array
     */
    protected $columns = [];

    /**
     *
     * @var array
     */
    protected $sortConditions = [];

    /**
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The data result
     *
     * @var \Zend\Paginator\Adapter\AdapterInterface
     */
    protected $paginatorAdapter;

    /**
     * Set the columns
     *
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     *
     * @return Column\AbstractColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set sort conditions
     *
     * @param Column\AbstractColumn $column
     * @param string                $sortDirection
     */
    public function addSortCondition(Column\AbstractColumn $column, $sortDirection = 'ASC')
    {
        $this->sortConditions[] = [
            'column'        => $column,
            'sortDirection' => $sortDirection,
        ];
    }

    public function setSortConditions(array $sortConditions)
    {
        $this->sortConditions = $sortConditions;
    }

    /**
     *
     * @return array
     */
    public function getSortConditions()
    {
        return $this->sortConditions;
    }

    /**
     * Add a filter rule
     *
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     *
     * @return \ZfcDatagrid\Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    public function setPaginatorAdapter(PaginatorAdapterInterface $paginator)
    {
        $this->paginatorAdapter = $paginator;
    }

    /**
     *
     * @return \Zend\Paginator\Adapter\AdapterInterface
     */
    public function getPaginatorAdapter()
    {
        return $this->paginatorAdapter;
    }
}
