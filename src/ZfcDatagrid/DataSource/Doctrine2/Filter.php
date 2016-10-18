<?php

namespace ZfcDatagrid\DataSource\Doctrine2;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter as DatagridFilter;

class Filter
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @param QueryBuilder $qb
     */
    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * @param DatagridFilter $filter
     *
     * @throws \Exception
     */
    public function applyFilter(DatagridFilter $filter)
    {
        $qb = $this->getQueryBuilder();
        $expr = new Expr();

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
        foreach ($values as $key => $value) {
            $valueParameterName = ':'.str_replace('.', '', $col->getUniqueId().$key);

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

        if (!empty($wheres)) {
            $orWhere = $qb->expr()->orX();
            $orWhere->addMultiple($wheres);

            $qb->andWhere($orWhere);
        }
    }
}
