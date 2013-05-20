<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\Filter;
use ZfcDatagrid\Datasource\Doctrine2Paginator as PaginatorAdapter;
use ZfcDatagrid\Column;
use Doctrine\ORM;
use Doctrine\ORM\Query\Expr;

class Doctrine2 extends AbstractDataSource
{

    /**
     *
     * @var ORM\QueryBuilder
     */
    private $queryBuilder;


    /**
     * Data source
     *
     * @param mixed $data            
     */
    public function __construct ($data)
    {
        if ($data instanceof ORM\QueryBuilder) {
            $this->queryBuilder = $data;
        } else {
            throw new \Exception("Unknown data input..." . get_class($data));
        }
    }

    public function execute ()
    {
        $queryBuilder = $this->queryBuilder;
        
        /**
         * Step 1) Apply needed columns
         */
        $selectColumns = array();
        foreach ($this->columns as $column) {
            if ($column instanceof Column\Standard && ! $column->hasDataPopulation()) {
                $colString = $column->getSelectPart1();
                if ($column->getSelectPart2() != '') {
                    $colString .= '.' . $column->getSelectPart2();
                }
                $colString .= ' ' . $column->getUniqueId();
                
                $selectColumns[] = $colString;
            }
        }
        $queryBuilder->resetDQLPart('select');
        $queryBuilder->select($selectColumns);
        
        /**
         * Step 2) Apply sorting
         */
        if (count($this->sortConditions) > 0) {
            // Minimum one sort condition given -> so reset the default orderBy
            $queryBuilder->resetDQLPart('orderBy');
            
            foreach ($this->sortConditions as $sortCondition) {
                $column = $sortCondition['column'];
                $queryBuilder->add('orderBy', new Expr\OrderBy($column->getUniqueId(), $sortCondition['sortDirection']), true);
            }
        }
        
        /**
         * Step 3) Apply filters
         */
        $whereExpressions = array();
        foreach ($this->filters as $filter) {
            /* @var $filter \ZfcDatagrid\Filter */
            if ($filter->isColumnFilter() === true) {
                $values = $filter->getValue();
                
                $column = $filter->getColumn();
                $colString = $column->getSelectPart1();
                if ($column->getSelectPart2() != '') {
                    $colString .= '.' . $column->getSelectPart2();
                }
                
                $whereExpressions[] = $this->getWhereExpression($filter->getOperator(), $colString, $values, $queryBuilder->expr());
            }
        }
        
        if (count($whereExpressions) > 0) {
            // Maybe WHERE are already existing...so keep it!
            $where = $queryBuilder->getDQLPart('where');
            
            $or = $queryBuilder->expr()->andX();
            $or->add($where);
            $or->addMultiple($whereExpressions);
            
            $queryBuilder->where($or);
        }
        
        /**
         * Step 4) Pagination
         */
        $this->setPaginatorAdapter(new PaginatorAdapter($queryBuilder));
    }

    /**
     * getWhereExpression
     *
     * @param string $operator            
     * @param string $colString            
     * @param array $values            
     * @throws \Exception
     * @return \Doctrine\ORM\Query\Expr
     */
    private function getWhereExpression ($operator, $colString, $values)
    {
        $expr = new Expr();
        
        switch ($operator) {
            
            case Filter::LIKE:
                return $expr->like($colString, $expr->literal('%' . $values[0] . '%'));
                break;
            
            case Filter::LIKE_LEFT:
                return $expr->like($colString, $expr->literal('%' . $values[0]));
                break;
            
            case Filter::LIKE_RIGHT:
                return $expr->like($colString, $expr->literal($values[0] . '%'));
                break;
            
            case Filter::NOT_LIKE:
                return $expr->notLike($colString, $expr->literal('%' . $values[0] . '%'));
                break;
            
            case Filter::NOT_LIKE_LEFT:
                return $expr->notLike($colString, $expr->literal('%' . $values[0]));
                break;
            
            case Filter::NOT_LIKE_RIGHT:
                return $expr->notLike($colString, $expr->literal($values[0] . '%'));
                break;
            
            case Filter::EQUAL:
                return $expr->eq($colString, $values[0]);
                break;
            
            case Filter::NOT_EQUAL:
                return $expr->neq($colString, $values[0]);
                break;
            
            case Filter::GREATER_EQUAL:
                return $expr->gte($colString, $values[0]);
                break;
            
            case Filter::GREATER:
                return $expr->gt($colString, $values[0]);
                break;
            
            case Filter::LESS_EQUAL:
                return $expr->lte($colString, $values[0]);
                break;
            
            case Filter::LESS:
                return $expr->lt($colString, $values[0]);
                break;
            
            case Filter::BETWEEN:
                return $expr->between($colString, $values[0], $values[1]);
                break;
            
            default:
                throw new \Exception('This operator is currently not supported: ' . $operator);
                break;
        }
    }
}
