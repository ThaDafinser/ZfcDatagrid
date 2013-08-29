<?php
namespace ZfcDatagrid\DataSource\Doctrine2;

use Zend\Paginator\Adapter\AdapterInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

class Paginator implements AdapterInterface
{

    /**
     *
     * @var QueryBuilder
     */
    protected $queryBuilder = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $rowCount = null;

    /**
     *
     * @param QueryBuilder $queryBuilder            
     */
    public function __construct (QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param integer $offset
     *            Page offset
     * @param integer $itemCountPerPage
     *            Number of items per page
     * @return array
     */
    public function getItems ($offset, $itemCountPerPage)
    {
        $queryBuilder = $this->queryBuilder;
        $queryBuilder->setFirstResult($offset)->setMaxResults($itemCountPerPage);
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count ()
    {
        if ($this->rowCount !== null) {
            return $this->rowCount;
        }
        
        $queryBuilder = $this->queryBuilder;
        $queryBuilder = clone $queryBuilder;
        
        $queryBuilder->setFirstResult(null)
            ->setMaxResults(null)
            ->resetDQLParts(array(
            'orderBy',
            'groupBy'
        ));
        
        $AST = $queryBuilder->getQuery()->getAST();
        
        $hasExpr = false;
        foreach ($AST->selectClause->selectExpressions as $selectExpressions) {
            if ($selectExpressions->expression instanceof Query\AST\AggregateExpression) {
                $hasExpr = true;
            }
        }
        
        if ($hasExpr === true) {
            $result = $queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
            $this->rowCount = count($result);
        } else {
            $queryBuilder->resetDQLPart('select');
            
            $fromPart = $queryBuilder->getDQLPart('from');
            $queryBuilder->select('COUNT(DISTINCT ' . $fromPart[0]->getAlias() . ')');
            
            try {
                $this->rowCount = $queryBuilder->getQuery()->getSingleScalarResult();
            } catch (\Exception $e) {
                // when the result is non unique its most likely that a group by was used
                // if so, we just get the complete result and count the number of results
                $result = $queryBuilder->getQuery()->getResult();
                $this->rowCount = count($result);
            }
        }
        
        return $this->rowCount;
        
        // $select = clone $this->select;
        // $select->reset(Select::COLUMNS);
        // $select->reset(Select::LIMIT);
        // $select->reset(Select::OFFSET);
        // $select->reset(Select::ORDER);
        // $select->reset(Select::GROUP);
        
        // // get join information, clear, and repopulate without columns
        // $joins = $select->getRawState(Select::JOINS);
        // $select->reset(Select::JOINS);
        // foreach ($joins as $join) {
        // $select->join($join['name'], $join['on'], array(), $join['type']);
        // }
        
        // $select->columns(array('c' => new Expression('COUNT(1)')));
        
        // $statement = $this->sql->prepareStatementForSqlObject($select);
        // $result = $statement->execute();
        // $row = $result->current();
        
        // $this->rowCount = $row['c'];
    }
}
