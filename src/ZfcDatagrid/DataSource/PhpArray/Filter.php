<?php

namespace ZfcDatagrid\DataSource\PhpArray;

use ZfcDatagrid\Filter as DatagridFilter;

class Filter
{
    /**
     * @var \ZfcDatagrid\Filter
     */
    private $filter;

    /**
     * @param \ZfcDatagrid\Filter $filter
     */
    public function __construct(DatagridFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return \ZfcDatagrid\Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Does the value get filtered?
     *
     * @param array $row
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function applyFilter(array $row)
    {
        $wasTrueOneTime = false;
        $isApply = false;

        foreach ($this->getFilter()->getValues() as $filterValue) {
            $filter = $this->getFilter();
            $col = $filter->getColumn();

            $value = $row[$col->getUniqueId()];
            $value = $col->getType()->getFilterValue($value);

            if ($filter->getOperator() == DatagridFilter::BETWEEN) {
                //BETWEEN have to be tested in one call
                $isApply = DatagridFilter::isApply($value, $this->getFilter()->getValues(), $filter->getOperator());

                return $isApply;
            } else {
                $isApply = DatagridFilter::isApply($value, $filterValue, $filter->getOperator());
            }
            if (true === $isApply) {
                $wasTrueOneTime = true;
            }

            switch ($filter->getOperator()) {

                case DatagridFilter::NOT_LIKE:
                case DatagridFilter::NOT_LIKE_LEFT:
                case DatagridFilter::NOT_LIKE_RIGHT:
                case DatagridFilter::NOT_EQUAL:
                case DatagridFilter::NOT_IN:
                    if (false === $isApply) {
                        // normally one "match" is okay -> so it's applied
                        // but e.g. NOT_LIKE is not allowed to match so even if the othere rules are true
                        // it has to fail!
                        return false;
                    }
                    break;
            }
        }

        if (false === $isApply && true === $wasTrueOneTime) {
            return true;
        }

        return $isApply;
    }
}
