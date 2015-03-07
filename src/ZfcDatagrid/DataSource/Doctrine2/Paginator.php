<?php
/**
 * This is just a proxy to detect if we can use the "fast" Pagination
 * or if we use the "safe" variant by Doctrine2
 *
 */
namespace ZfcDatagrid\DataSource\Doctrine2;

use Zend\Paginator\Adapter\AdapterInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as Doctrine2Paginator;
use ZfcDatagrid\DataSource\Doctrine2\PaginatorFast as ZfcDatagridPaginator;

class Paginator implements AdapterInterface
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
     * @var \Doctrine\ORM\Tools\Pagination\Paginator
     */
    private $paginator;

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
     * Test which pagination solution to use
     *
     * @return boolean
     */
    private function useCustomPaginator()
    {
        $qb = $this->getQueryBuilder();
        $parts = $qb->getDQLParts();

        if ($parts['having'] !== null || true === $parts['distinct']) {
            // never tried having in such queries...
            return false;
        }

        // @todo maybe more detection needed :-/
        return true;
    }

    /**
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    private function getPaginator()
    {
        if ($this->paginator !== null) {
            return $this->paginator;
        }

        if ($this->useCustomPaginator() === true) {
            $this->paginator = new ZfcDatagridPaginator($this->getQueryBuilder());
        } else {
            // Doctrine2Paginator as fallback...they are using 3 queries
            $this->paginator = new Doctrine2Paginator($this->getQueryBuilder());
        }

        return $this->paginator;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset
     * @param  integer $itemCountPerPage
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $paginator = $this->getPaginator();
        if ($paginator instanceof Doctrine2Paginator) {
            $this->getQueryBuilder()
                ->setFirstResult($offset)
                ->setMaxResults($itemCountPerPage);

            return $paginator->getIterator()->getArrayCopy();
        } else {
            return $paginator->getItems($offset, $itemCountPerPage);
        }
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        return $this->getPaginator()->count();
    }
}
