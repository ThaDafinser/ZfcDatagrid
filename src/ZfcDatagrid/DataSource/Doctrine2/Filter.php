<?php
namespace ZfcDatagrid\DataSource\Doctrine2;

use ZfcDatagrid\Filter as DatagridFilter;
use ZfcDatagrid\Column;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

class Filter
{
    /**
     *
     * @var QueryBuilder
     */
    private $qb;

    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * @param  DatagridFilter            $filter
     * @throws \InvalidArgumentException
     */
    public function applyFilter(DatagridFilter $filter)
    {
        $qb = $this->getQueryBuilder();
        $expr = new Expr();

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
        foreach ($values as $key => $value) {
            $valueParameterName = ':'.str_replace('.', '', $column->getUniqueId().$key);

            switch ($filter->getOperator()) {

                case DatagridFilter::LIKE:
                    $wheres[] = $expr->like($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, '%'.$value.'%');
                    break;

                case DatagridFilter::LIKE_LEFT:
                    $wheres[] = $expr->like($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, '%'.$value);
                    break;

                case DatagridFilter::LIKE_RIGHT:
                    $wheres[] = $expr->like($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, $value.'%');
                    break;

                case DatagridFilter::NOT_LIKE:
                    $wheres[] = $expr->notLike($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, '%'.$value.'%');
                    break;

                case DatagridFilter::NOT_LIKE_LEFT:
                    $wheres[] = $expr->notLike($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, '%'.$value);
                    break;

                case DatagridFilter::NOT_LIKE_RIGHT:
                    $wheres[] = $expr->notLike($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, $value.'%');
                    break;

                case DatagridFilter::EQUAL:
                    $wheres[] = $expr->eq($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, $value);
                    break;

                case DatagridFilter::NOT_EQUAL:
                    $wheres[] = $expr->neq($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, $value);
                    break;

                case DatagridFilter::GREATER_EQUAL:
                    $wheres[] = $expr->gte($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, $value);
                    break;

                case DatagridFilter::GREATER:
                    $wheres[] = $expr->gt($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, $value);
                    break;

                case DatagridFilter::LESS_EQUAL:
                    $wheres[] = $expr->lte($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, $value);
                    break;

                case DatagridFilter::LESS:
                    $wheres[] = $expr->lt($colString, $valueParameterName);
                    $qb->setParameter($valueParameterName, $value);
                    break;

                case DatagridFilter::BETWEEN:
                    $minParameterName = ':'.str_replace('.', '', $colString.'0');
                    $maxParameterName = ':'.str_replace('.', '', $colString.'1');

                    $wheres[] = $expr->between($colString, $minParameterName, $maxParameterName);

                    $qb->setParameter($minParameterName, $values[0]);
                    $qb->setParameter($maxParameterName, $values[1]);
                    break 2;

                default:
                    throw new \InvalidArgumentException('This operator is currently not supported: '.$filter->getOperator());
                    break;
            }
        }

        if (count($wheres) > 0) {
            $orWhere = $qb->expr()->orX();
            $orWhere->addMultiple($wheres);

            $qb->andWhere($orWhere);
        }
    }
}
