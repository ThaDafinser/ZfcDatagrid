<?php

namespace ZfcDatagrid\DataSource\Doctrine2;

use Doctrine\ORM\QueryBuilder;
use Zend\Paginator\Adapter\AdapterInterface;

class PaginatorFast implements AdapterInterface
{
    /**
     * @var QueryBuilder
     */
    protected $qb = null;

    /**
     * Total item count.
     *
     * @var int
     */
    protected $rowCount = null;

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
     * Returns an array of items for a page.
     *
     * @param int $offset
     * @param int $itemCountPerPage
     *
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
     * @return int
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

        /*
         * Reset things
         */
        $qb->setFirstResult(null)
            ->setMaxResults(null)
            ->resetDQLParts([
            'orderBy',
            'select',
        ]);

        if (count($groupParts) > 1) {
            /*
             * UGLY WORKAROUND!!! @todo
             */
            // more than one group part...tricky!
            // @todo finde something better...
            $qb->resetDQLPart('groupBy');
            $qb->select('CONCAT('.implode(',', $groupParts).') as uniqueParts');

            $items = [];
            $result = $qb->getQuery()->getResult();
            foreach ($result as $row) {
                $items[] = $row['uniqueParts'];
            }
            $uniqueItems = array_unique($items);

            $this->rowCount = count($uniqueItems);
        } elseif (count($groupParts) == 1) {
            $groupPart = $groupParts[0];

            $qb->resetDQLPart('groupBy');
            $qb->select('COUNT(DISTINCT '.$groupPart.')');

            $this->rowCount = $qb->getQuery()->getSingleScalarResult();
        } else {
            // NO GROUP BY
            $countOneFunction = $qb->getEntityManager()
                ->getConfiguration()
                ->getCustomStringFunction('COUNT_ONE');
            if ($countOneFunction !== null) {
                $qb->select('COUNT_ONE() AS rowCount');
            } else {
                $fromPart = $dqlParts['from'];
                $qb->select('COUNT('.$fromPart[0]->getAlias().')');
            }

            $this->rowCount = $qb->getQuery()->getSingleScalarResult();
        }

        return $this->rowCount;
    }
}
