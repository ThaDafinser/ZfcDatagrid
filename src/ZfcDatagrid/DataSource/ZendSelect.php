<?php
namespace ZfcDatagrid\DataSource;

use Zend\Paginator\Adapter\DbSelect as PaginatorAdapter;
use ZfcDatagrid\Column\AbstractColumn;
use Zend\Db\Sql;
use ZfcDatagrid\Column;

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
        $select = $this->select;
        
        /**
         * Step 1) Apply needed columns
         */
        $selectColumns = array();
        foreach ($this->columns as $column) {
            if ($column instanceof Column\Standard) {
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
        // echo $select->getSqlString($this->adapterOrSqlObject->getPlatform());
        // exit();
        
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
