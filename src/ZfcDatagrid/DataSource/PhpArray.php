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

    /**
     *
     * @param unknown $sortCondition            
     * @return array
     */
    private function getSortArrayParameter($sortCondition)
    {
        $sortArray = array(
            $sortCondition['column']->getSelectPart1()
        );
        
        if ($sortCondition['sortDirection'] === 'DESC') {
            $desc = SORT_DESC;
            $sortArray[] = $desc;
        } else {
            $asc = SORT_ASC;
            $sortArray[] = $asc;
        }
        
        // @todo Based on the column type -> SORT_NUMERIC, SORT_STRING, SORT_NATURAL, ...
        // $type = SORT_NUMERIC;
        switch (get_class($sortCondition['column']->getType())) {
            
            case 'ZfcDatagrid\Column\Type\Number':
                $numeric = SORT_NUMERIC;
                $sortArray[] = $numeric;
                break;
            
            default:
                $regular = SORT_REGULAR;
                $sortArray[] = $regular;
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
        $sortArguments = array();
        foreach ($sortConditions as $sortCondition) {
            $sortParameters = $this->getSortArrayParameter($sortCondition);
            
            // fetch column data
            $column = $sortParameters[0];
            
            $dataCol = array();
            foreach ($data as $key => $row) {
                if (! isset($row[$column])) {
                    $value = '';
                } else {
                    $value = $row[$column];
                }
                $dataCol[$key] = $value;
            }
            
            $sortArguments[] = array(
                $dataCol,
                $sortParameters[1],
                $sortParameters[2]
            );
        }
        
        return $this->applyMultiSort($data, $sortArguments);
    }

    /**
     * Multisort an array
     *
     * @param array $data            
     * @param array $sortArguments            
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    private function applyMultiSort(array $data, array $sortArguments)
    {
        $args = array();
        foreach ($sortArguments as $values) {
            $remain = count($values) % 3;
            if ($remain != 0) {
                throw new \InvalidArgumentException('The parameter count for each sortArgument has to be three. Given count of: ' . count($values));
            }
            $args[] = $values[0]; // column value
            $args[] = $values[1]; // sort direction
            $args[] = $values[2]; // sort type
        }
        
        $args[] = $data;
        
        //possible 5.3.3 fix?
        $sortArgs = array();
        foreach ($args as $key => &$value) {
            $sortArgs[$key] = &$value;
        }
        
        call_user_func_array('array_multisort', $sortArgs);
        
        return end($args);
    }
}
