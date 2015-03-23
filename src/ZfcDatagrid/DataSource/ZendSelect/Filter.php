<?php
namespace ZfcDatagrid\DataSource\ZendSelect;

use ZfcDatagrid\Filter as DatagridFilter;
use ZfcDatagrid\Column;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\PredicateSet;

class Filter
{
    /**
     *
     * @var Sql
     */
    private $sql;

    /**
     *
     * @var Select
     */
    private $select;

    public function __construct(Sql $sql, Select $select)
    {
        $this->sql = $sql;
        $this->select = $select;
    }

    /**
     *
     * @return \Zend\Db\Sql\Sql
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param  DatagridFilter            $filter
     * @throws \InvalidArgumentException
     */
    public function applyFilter(DatagridFilter $filter)
    {
        $select = $this->getSelect();

        $adapter = $this->getSql()->getAdapter();
        $qi = function ($name) use ($adapter) {
            return $adapter->getPlatform()->quoteIdentifier($name);
        };

        $column = $filter->getColumn();
        $colString = $column->getSelectPart1();
        if ($column->getSelectPart2() != '') {
            $colString .= '.'.$column->getSelectPart2();
        }
        if ($column instanceof Column\Select && $column->hasFilterSelectExpression()) {
            $colString = sprintf($column->getFilterSelectExpression(), $colString);
        }
        $values = $filter->getValues();

        $wheres = array();
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
                    $wheres[] = $where->literal($qi($colString).'NOT LIKE ?', array(
                        '%'.$value.'%',
                    ));
                    break;

                case DatagridFilter::NOT_LIKE_LEFT:
                    $wheres[] = $where->literal($qi($colString).'NOT LIKE ?', array(
                        '%'.$value,
                    ));
                    break;

                case DatagridFilter::NOT_LIKE_RIGHT:
                    $wheres[] = $where->literal($qi($colString).'NOT LIKE ?', array(
                        $value.'%',
                    ));
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

        if (count($wheres) > 0) {
            $set = new PredicateSet($wheres, PredicateSet::OP_OR);
            $select->where->andPredicate($set);
        }
    }
}
