<?php
namespace ZfcDatagrid\DataSource;

use Zend\Paginator\Adapter\ArrayAdapter as PaginatorAdapter;
use ZfcDatagrid\Column\AbstractColumn;

class PhpArray implements DataSourceInterface
{

    private $data = array();

    private $columns = array();

    private $sortConditions = array();

    /**
     *
     * @var PaginatorAdapter
     */
    private $paginatorAdapter;

    /**
     * Set the data source
     *
     * @param array $data            
     */
    public function __construct ($data)
    {
        if (is_array($data)) {
            $this->data = $data;
        } else {
            throw new \Exception("Unsupported data input...");
        }
    }

    /**
     * Set the columns
     */
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

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements
     */
    public function execute ()
    {
        $data = $this->data;
        
        /**
         * Step 1) Remove unneeded columns
         */
        // @todo ? Better performance or let it be?
        
        /**
         * Step 2) Apply sorting
         *
         * @see http://php.net/manual/de/function.array-multisort.php
         * @see example number 3
         */
        if (count($this->sortConditions) > 0) {
            $sortConditions = $this->sortConditions;
            
            // @todo UGLY SOLUTION!!!
            if (count($sortConditions) === 1) {
                $sort1 = $this->getSortArrayParameter($sortConditions[0]);
                
                $data = $this->sortArrayMultiple($data, $sort1[0], $sort1[1]);
            } elseif (count($sortConditions) === 2) {
                $sort1 = $this->getSortArrayParameter($sortConditions[0]);
                $sort2 = $this->getSortArrayParameter($sortConditions[1]);
                
                $data = $this->sortArrayMultiple($data, $sort1[0], $sort1[1], $sort2[0], $sort2[1]);
            } else {
                throw new \Exception('Too much column sorts defined!');
            }
        }
        
        /**
         * Step 3) Apply filters
         */
        
        /**
         * Step 4) Pagination
         */
        $this->paginatorAdapter = new PaginatorAdapter($data);
        $this->data = $data;
    }

    /**
     * Get the paginator adapter
     *
     * @return \Zend\Paginator\Adapter\AdapterInterface
     */
    public function getPaginatorAdapter ()
    {
        return $this->paginatorAdapter;
    }

    private function getSortArrayParameter ($sortCondition)
    {
        $direction = SORT_ASC;
        if ($sortCondition['sortDirection'] === 'DESC') {
            $direction = SORT_DESC;
        }
        
        // @todo Based on the column type -> SORT_NUMERIC, SORT_STRING, SORT_NATURAL, ...
        // $type = SORT_NUMERIC;
        
        return array(
            $sortCondition['column']->getSelectPart1(),
            $direction
        );
    }

    /**
     *
     * @see http://php.net/manual/de/function.array-multisort.php Example in comments: array_orderby()
     * @author jimpoz at jimpoz dot com
     * @return array
     */
    private function sortArrayMultiple ()
    {
        $args = func_get_args();
        $data = array_shift($args);
        
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        
        return array_pop($args);
    }
}