D<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Column\AbstractColumn;

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
    public function __construct ($data);

    /**
     * Set the columns
     */
    public function setColumns (array $columns);

    /**
     * Set sort conditions
     *
     * @param AbstractColumn $column
     * @param string $sortDirection            
     */
    public function addSortCondition (AbstractColumn $column, $sortDirection = 'ASC');

    /**
     * Add a filter rule
     */
    public function addFilter ();

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements
     */
    public function execute ();

    /**
     * Get the paginator adapter
     *
     * @return \Zend\Paginator\Adapter\AdapterInterface
     */
    public function getPaginatorAdapter ();
}
