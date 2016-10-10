<?php

namespace ZfcDatagrid\DataSource\ZendSelect;

use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter as DatagridFilter;

class Filter
{
    /**
     * @var Sql
     */
    private $sql;

    /**
     * @var Select
     */
    private $select;

    public function __construct(Sql $sql, Select $select)
    {
        $this->sql = $sql;
        $this->select = $select;
    }

    /**
     * @return \Zend\Db\Sql\Sql
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param DatagridFilter $filter
     *
     * @throws \Exception
     */
    public function applyFilter(DatagridFilter $filter)
    {
        $select = $this->getSelect();

        $adapter = $this->getSql()->getAdapter();
        $qi = function ($name) use ($adapter) {
            return $adapter->getPlatform()->quoteIdentifier($name);
        };

        $col = $filter->getColumn();
        if (!$col instanceof Column\Select) {
            throw new \Exception('This column cannot be filtered: '.$col->getUniqueId());
        }

        $colString = $col->getSelectPart1();
        if ($col->getSelectPart2() != '') {
            $colString .= '.'.$col->getSelectPart2();
        }
        if ($col instanceof Column\Select && $col->hasFilterSelectExpression()) {
            $colString = sprintf($col->getFilterSelectExpression(), $colString);
        }
        $values = $filter->getValues();

        $wheres = [];
        foreach ($values as $value) {
            $where = new Where();

            switch ($filter->getOperator()) {

                case DatagridFilter::LIKE:
                    $wheres[] = $where->like($colString, '%'.$value.'%');
                    break;

                case DatagridFilter::LIKE_LEFT:
                    $wheres[] = $where->like($colString, '%'.$value);
                    break;

                case DatagridFilter::LIKE_RIGHT:
                    $wheres[] = $where->like($colString, $value.'%');
                    break;

                case DatagridFilter::NOT_LIKE:
                    $wheres[] = $where->literal($qi($colString).'NOT LIKE ?', [
                        '%'.$value.'%',
                    ]);
                    break;

                case DatagridFilter::NOT_LIKE_LEFT:
                    $wheres[] = $where->literal($qi($colString).'NOT LIKE ?', [
                        '%'.$value,
                    ]);
                    break;

                case DatagridFilter::NOT_LIKE_RIGHT:
                    $wheres[] = $where->literal($qi($colString).'NOT LIKE ?', [
                        $value.'%',
                    ]);
                    break;

                case DatagridFilter::EQUAL:
                    $wheres[] = $where->equalTo($colString, $value);
                    break;

                case DatagridFilter::NOT_EQUAL:
                    $wheres[] = $where->notEqualTo($colString, $value);
                    break;

                case DatagridFilter::GREATER_EQUAL:
                    $wheres[] = $where->greaterThanOrEqualTo($colString, $value);
                    break;

                case DatagridFilter::GREATER:
                    $wheres[] = $where->greaterThan($colString, $value);
                    break;

                case DatagridFilter::LESS_EQUAL:
                    $wheres[] = $where->lessThanOrEqualTo($colString, $value);
                    break;

                case DatagridFilter::LESS:
                    $wheres[] = $where->lessThan($colString, $value);
                    break;

                case DatagridFilter::BETWEEN:
                    $wheres[] = $where->between($colString, $values[0], $values[1]);
                    break 2;

                default:
                    throw new \InvalidArgumentException('This operator is currently not supported: '.$filter->getOperator());
                    break;
            }
        }

        if (!empty($wheres)) {
            $set = new PredicateSet($wheres, PredicateSet::OP_OR);
            $select->where->andPredicate($set);
        }
    }
}
