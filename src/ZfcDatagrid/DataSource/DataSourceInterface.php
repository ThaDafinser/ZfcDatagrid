<?php
namespace ZfcDatagrid\DataSource;

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
     * Get the data back from construct
     *
     * @return mixed
     */
    public function getData();

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements
     */
    public function execute();
}
