<?php
namespace ZfcDatagrid\DataSource;

use Zend\Paginator\Adapter\ArrayAdapter as PaginatorAdapter;

class PhpArray extends AbstractDataSource
{

    private $data = array();

    /**
     * Set the data source
     *
     * @param array $data            
     */
    public function __construct($data)
    {
        if (is_array($data)) {
            $this->data = $data;
        } else {
            throw new \InvalidArgumentException("Unsupported data input, please provide an array");
        }
    }

    /**
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements
     */
    public function execute()
    {
        $data = $this->getData();
        
        /**
         * Step 1) Apply sorting
         *
         * @see http://php.net/manual/de/function.array-multisort.php
         * @see example number 3
         */
        if (count($this->getSortConditions()) > 0) {
            $data = $this->sortArrayMultiple($data, $this->getSortConditions());
        }
        
        /**
         * Step 2) Apply filters
         */
        foreach ($this->getFilters() as $filter) {
            /* @var $filter \ZfcDatagrid\Filter */
            if ($filter->isColumnFilter() === true) {
                $data = array_filter($data, array(
                    new PhpArray\Filter($filter),
                    'applyFilter'
                ));
            }
        }
        
        /**
         * Step 3) Remove unneeded columns
         *
         * @todo ? Better performance or let it be?
         */
        $selectedColumns = array();
        foreach ($this->getColumns() as $column) {
            $selectedColumns[] = $column->getUniqueId();
        }
        
        foreach ($data as &$row) {
            foreach ($row as $keyRowCol => $rowCol) {
                if (! in_array($keyRowCol, $selectedColumns)) {
                    unset($row[$keyRowCol]);
                }
            }
        }
        
        /**
         * Step 4) Pagination
         */
        $this->setPaginatorAdapter(new PaginatorAdapter($data));
    }

    private function getSortArrayParameter($sortCondition)
    {
        $sortArray = array(
            $sortCondition['column']->getSelectPart1()
        );
        
        $direction = SORT_ASC;
        if ($sortCondition['sortDirection'] === 'DESC') {
            $direction = SORT_DESC;
        }
        $sortArray[] = $direction;
        
        // @todo Based on the column type -> SORT_NUMERIC, SORT_STRING, SORT_NATURAL, ...
        // $type = SORT_NUMERIC;
        switch (get_class($sortCondition['column']->getType())) {
            
            case 'ZfcDatagrid\Column\Type\Number':
                $sortArray[] = SORT_NUMERIC;
                break;
        }
        
        return $sortArray;
    }

    /**
     *
     * @see http://php.net/manual/de/function.array-multisort.php Example in comments: array_orderby()
     * @author jimpoz at jimpoz dot com
     * @return array
     */
    private function sortArrayMultiple(array $data, $sortConditions)
    {
        $arguments = array();
        
        $i = 1;
        foreach ($sortConditions as $sortCondition) {
            $sortParameters = $this->getSortArrayParameter($sortCondition);
            
            $column = array_shift($sortParameters);
            
            $dataCol = array();
            foreach ($data as $key => $row) {
                $dataCol[$key] = $row[$column];
            }
            
            $arguments[] = $dataCol;
            foreach ($sortParameters as $parameter) {
                $arguments[] = $parameter;
            }
        }
        
        $arguments[] = &$data;
        
        call_user_func_array('array_multisort', $arguments);
        
        return array_pop($arguments);
    }
}
