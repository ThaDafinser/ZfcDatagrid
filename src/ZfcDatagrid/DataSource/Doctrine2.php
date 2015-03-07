<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\DataSource\Doctrine2\Paginator as PaginatorAdapter;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use Doctrine\ORM;
use Doctrine\ORM\Query\Expr;

class Doctrine2 extends AbstractDataSource
{
    /**
     *
     * @var ORM\QueryBuilder
     */
    private $qb;

    /**
     * Data source
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        if ($data instanceof ORM\QueryBuilder) {
            $this->qb = $data;
        } else {
            $return = $data;
            if (is_object($data)) {
                $return = get_class($return);
            }
            throw new \InvalidArgumentException("Unknown data input...".$return);
        }
    }

    /**
     *
     * @return ORM\QueryBuilder
     */
    public function getData()
    {
        return $this->qb;
    }

    public function execute()
    {
        $qb = $this->getData();

        /*
         * Step 1) Apply needed columns
         */
        $selectColumns = array();
        foreach ($this->getColumns() as $column) {
            if ($column instanceof Column\Select) {
                $colString = $column->getSelectPart1();
                if ($column->getSelectPart2() != '') {
                    $colString .= '.'.$column->getSelectPart2();
                }
                $colString .= ' '.$column->getUniqueId();

                $selectColumns[] = $colString;
            }
        }
        $qb->resetDQLPart('select');
        $qb->select($selectColumns);

        /*
         * Step 2) Apply sorting
         */
        if (count($this->getSortConditions()) > 0) {
            // Minimum one sort condition given -> so reset the default orderBy
            $qb->resetDQLPart('orderBy');

            foreach ($this->getSortConditions() as $key => $sortCondition) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                $column = $sortCondition['column'];

                $colString = $column->getSelectPart1();
                if ($column->getSelectPart2() != '') {
                    $colString .= '.'.$column->getSelectPart2();
                }

                if ($column->getType() instanceof Type\Number) {
                    $qb->addSelect('ABS('.$colString.') sortColumn'.$key);
                    $qb->add('orderBy', new Expr\OrderBy('sortColumn'.$key, $sortCondition['sortDirection']), true);
                } else {
                    $qb->add('orderBy', new Expr\OrderBy($column->getUniqueId(), $sortCondition['sortDirection']), true);
                }
            }
        }

        /*
         * Step 3) Apply filters
         */
        $filterColumn = new Doctrine2\Filter($qb);
        foreach ($this->getFilters() as $filter) {
            /* @var $filter \ZfcDatagrid\Filter */
            if ($filter->isColumnFilter() === true) {
                $filterColumn->applyFilter($filter);
            }
        }

        /*
         * Step 4) Pagination
         */
        $this->setPaginatorAdapter(new PaginatorAdapter($qb));
    }
}
