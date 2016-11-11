<?php

namespace ZfcDatagrid\DataSource;

use Zend\Paginator\Adapter\ArrayAdapter as PaginatorAdapter;
use ZfcDatagrid\Column;

class PhpArray extends AbstractDataSource
{
    private $data = [];

    /**
     * Set the data source.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        if (is_array($data)) {
            $this->data = $data;
        } else {
            throw new \InvalidArgumentException('Unsupported data input, please provide an array');
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements.
     */
    public function execute()
    {
        $data = $this->getData();

        /*
         * Step 1) Apply sorting
         *
         * @see http://php.net/manual/de/function.array-multisort.php
         * @see example number 3
         */
        if (count($this->getSortConditions()) > 0) {
            $data = $this->sortArrayMultiple($data, $this->getSortConditions());
        }

        /*
         * Step 2) Apply filters
         */
        foreach ($this->getFilters() as $filter) {
            /* @var $filter \ZfcDatagrid\Filter */
            if ($filter->isColumnFilter() === true) {
                $data = array_filter($data, [
                    new PhpArray\Filter($filter),
                    'applyFilter',
                ]);
            }
        }

        /**
         * Step 3) Remove unneeded columns.
         *
         * @todo ? Better performance or let it be?
         */
        $selectedColumns = [];
        foreach ($this->getColumns() as $col) {
            if (!$col instanceof Column\Select) {
                continue;
            }

            $colString = $col->getSelectPart1();
            if ($col->getSelectPart2() != '') {
                $colString = $col->getSelectPart1().'_'.$col->getSelectPart2();
            }

            $selectedColumns[] = $colString;
        }

        foreach ($data as &$row) {
            foreach ($row as $keyRowCol => $rowCol) {
                if (!in_array($keyRowCol, $selectedColumns)) {
                    unset($row[$keyRowCol]);
                }
            }
        }

        /*
         * Step 4) Pagination
         */
        $this->setPaginatorAdapter(new PaginatorAdapter($data));
    }

    /**
     * @param array $sortCondition
     *
     * @return array
     */
    private function getSortArrayParameter($sortCondition)
    {
        $sortArray = [
            $sortCondition['column']->getUniqueId(),
        ];

        if ('DESC' === $sortCondition['sortDirection']) {
            $desc = SORT_DESC;
            $sortArray[] = $desc;
        } else {
            $asc = SORT_ASC;
            $sortArray[] = $asc;
        }

        switch (get_class($sortCondition['column']->getType())) {

            case Column\Type\Number::class:
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
     * @see http://php.net/manual/de/function.array-multisort.php Example in comments: array_orderby()
     *
     * @author jimpoz at jimpoz dot com
     *
     * @return array
     */
    private function sortArrayMultiple(array $data, $sortConditions)
    {
        $sortArguments = [];
        foreach ($sortConditions as $sortCondition) {
            $sortParameters = $this->getSortArrayParameter($sortCondition);

            // fetch column data
            $column = $sortParameters[0];

            $dataCol = [];
            foreach ($data as $key => $row) {
                if (!isset($row[$column])) {
                    $value = '';
                } else {
                    $value = $row[$column];
                }
                $dataCol[$key] = $value;
            }

            $sortArguments[] = [
                $dataCol,
                $sortParameters[1],
                $sortParameters[2],
            ];
        }

        return $this->applyMultiSort($data, $sortArguments);
    }

    /**
     * Multisort an array.
     *
     * @param array $data
     * @param array $sortArguments
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    private function applyMultiSort(array $data, array $sortArguments)
    {
        $args = [];
        foreach ($sortArguments as $values) {
            $remain = count($values) % 3;
            if ($remain != 0) {
                throw new \InvalidArgumentException('The parameter count for each sortArgument has to be three. Given count of: '.count($values));
            }
            $args[] = $values[0]; // column value
            $args[] = $values[1]; // sort direction
            $args[] = $values[2]; // sort type
        }

        $args[] = $data;

        //possible 5.3.3 fix?
        $sortArgs = [];
        foreach ($args as $key => &$value) {
            $sortArgs[$key] = &$value;
        }

        call_user_func_array('array_multisort', $sortArgs);

        return end($args);
    }
}
