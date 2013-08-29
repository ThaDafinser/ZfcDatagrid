<?php
namespace ZfcDatagrid\DataSource\Doctrine2;

use Zend\Paginator\Adapter\AdapterInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

class PaginatorFast implements AdapterInterface
{

    /**
     *
     * @var QueryBuilder
     */
    protected $qb = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $rowCount = null;

    /**
     *
     * @param QueryBuilder $qb            
     */
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
     * Returns an array of items for a page.
     *
     * @param integer $offset            
     * @param integer $itemCountPerPage            
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $qb = $this->getQueryBuilder();
        $qb->setFirstResult($offset)->setMaxResults($itemCountPerPage);
        
        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * Partly adapted from Zend
     *
     * @see https://github.com/zendframework/zf1/blob/master/library/Zend/Paginator/Adapter/DbSelect.php#L198
     *
     * @return integer
     */
    public function count()
    {
        if ($this->rowCount !== null) {
            return $this->rowCount;
        }
        
        $qbOriginal = $this->getQueryBuilder();
        $qb = clone $qbOriginal;
        $dqlParts = $qb->getDQLParts();
        $groupParts = $dqlParts['groupBy'];
        $selectParts = $dqlParts['select'];
        // var_dump($selectParts);
        // exit();
        
        /*
         * Reset things
         */
        $qb->setFirstResult(null)
            ->setMaxResults(null)
            ->resetDQLParts(array(
            'orderBy',
            'select'
        ));
        
        if ($groupParts !== null) {
            $groupPart = $groupParts[0];
            
            // var_dump($groupPart);
            // echo $groupPart;exit();
            $qb->resetDQLPart('groupBy');
            $qb->select('COUNT(DISTINCT ' . $groupPart . ')');
            
            $this->rowCount = $qb->getQuery()->getSingleScalarResult();
        } else {
            // NO GROUP BY
            $qb->select('COUNT_ONE() AS rowCount');
            
            $this->rowCount = $qb->getQuery()->getSingleScalarResult();
        }
        
        return $this->rowCount;
        
        /*
         * First reset/unset unnecessary things for counting
         */
        // $qb->setFirstResult(null)
        // ->setMaxResults(null)
        // ->resetDQLParts(array(
        // 'orderBy',
        // 'groupBy',
        // 'select'
        // ));
        
        $hasExpr = false;
        foreach ($AST->selectClause->selectExpressions as $selectExpressions) {
            if ($selectExpressions->expression instanceof Query\AST\AggregateExpression) {
                $hasExpr = true;
            }
        }
        
        if ($hasExpr === true) {
            $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
            $this->rowCount = count($result);
        } else {
            $qb->resetDQLPart('select');
            
            $fromPart = $qb->getDQLPart('from');
            $qb->select('COUNT(DISTINCT ' . $fromPart[0]->getAlias() . ')');
            
            try {
                $this->rowCount = $qb->getQuery()->getSingleScalarResult();
            } catch (\Exception $e) {
                // when the result is non unique its most likely that a group by was used
                // if so, we just get the complete result and count the number of results
                $result = $qb->getQuery()->getResult();
                $this->rowCount = count($result);
            }
        }
        
        return $this->rowCount;
    }
}
