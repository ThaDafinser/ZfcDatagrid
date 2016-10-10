<?php

namespace ZfcDatagrid\DataSource;

use Zend\Paginator\Adapter\AdapterInterface as PaginatorAdapterInterface;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

abstract class AbstractDataSource implements DataSourceInterface
{
    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $sortConditions = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * The data result.
     *
     * @var \Zend\Paginator\Adapter\AdapterInterface
     */
    protected $paginatorAdapter;

    /**
     * Set the data source
     * - array
     * - ZF2: Zend\Db\Sql\Select
     * - Doctrine2: Doctrine\ORM\QueryBuilder
     * - ...
     *
     * @param mixed $data
     *
     * @throws \Exception
     */
    public function __construct($data)
    {
        // we need this exception, because a abstract __construct, create a exception in php-unit for mocking
        throw new \Exception(sprintf('Missing __construct in %s', get_class($this)));
    }

    /**
     * Set the columns.
     *
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return Column\AbstractColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set sort conditions.
     *
     * @param Column\AbstractColumn $column
     * @param string                $sortDirection
     */
    public function addSortCondition(Column\AbstractColumn $column, $sortDirection = 'ASC')
    {
        $this->sortConditions[] = [
            'column' => $column,
            'sortDirection' => $sortDirection,
        ];
    }

    /**
     * @param array $sortConditions
     */
    public function setSortConditions(array $sortConditions)
    {
        $this->sortConditions = $sortConditions;
    }

    /**
     * @return array
     */
    public function getSortConditions()
    {
        return $this->sortConditions;
    }

    /**
     * Add a filter rule.
     *
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return \ZfcDatagrid\Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param PaginatorAdapterInterface $paginator
     */
    public function setPaginatorAdapter(PaginatorAdapterInterface $paginator)
    {
        $this->paginatorAdapter = $paginator;
    }

    /**
     * @return \Zend\Paginator\Adapter\AdapterInterface
     */
    public function getPaginatorAdapter()
    {
        return $this->paginatorAdapter;
    }

    /**
     * Get the data back from construct.
     *
     * @return mixed
     */
    abstract public function getData();

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements.
     */
    abstract public function execute();
}
