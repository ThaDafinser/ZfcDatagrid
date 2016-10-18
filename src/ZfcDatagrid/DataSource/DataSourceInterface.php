<?php

namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

interface DataSourceInterface
{
    /**
     * Set the data source
     * - array
     * - ZF2: Zend\Db\Sql\Select
     * - Doctrine2: Doctrine\ORM\QueryBuilder
     * - ...
     *
     * @param mixed $data
     */
    public function __construct($data);

    /**
     * Get the data back from construct.
     *
     * @return mixed
     */
    public function getData();

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements.
     */
    public function execute();

    /**
     * Set the columns.
     *
     * @param array $columns
     */
    public function setColumns(array $columns);

    /**
     * Set sort conditions.
     *
     * @param Column\AbstractColumn $column
     * @param string                $sortDirection
     */
    public function addSortCondition(Column\AbstractColumn $column, $sortDirection = 'ASC');

    /**
     * @param Filter $filters
     */
    public function addFilter(Filter $filter);

    /**
     * @return \Zend\Paginator\Adapter\AdapterInterface
     */
    public function getPaginatorAdapter();
}
