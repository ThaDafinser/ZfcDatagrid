<?php

namespace ZfcDatagrid\DataSource;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use ZfcDatagrid\Column;
use ZfcDatagrid\DataSource\PhpArray as SourceArray;

class Doctrine2Collection extends AbstractDataSource
{
    /**
     * @var Collection
     */
    private $data;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Data source.
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        if ($data instanceof Collection) {
            $this->data = $data;
        } else {
            $return = $data;
            if (is_object($data)) {
                $return = 'instanceof '.get_class($return);
            }
            throw new \InvalidArgumentException('Unknown data input: "'.$return.'"');
        }
    }

    /**
     * @return Collection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    public function execute()
    {
        $hydrator = new DoctrineHydrator($this->getEntityManager());

        $dataPrepared = [];
        foreach ($this->getData() as $row) {
            $dataExtracted = $hydrator->extract($row);

            $rowExtracted = [];
            foreach ($this->getColumns() as $col) {
                /* @var $col \ZfcDatagrid\Column\Select */
                if (!$col instanceof Column\Select) {
                    continue;
                }

                $part1 = $col->getSelectPart1();
                $part2 = $col->getSelectPart2();

                if (null === $part2) {
                    if (isset($dataExtracted[$part1])) {
                        $rowExtracted[$col->getUniqueId()] = $dataExtracted[$part1];
                    }
                } else {
                    // NESTED
                    if (isset($dataExtracted[$part1])) {
                        $dataExtractedNested = $hydrator->extract($dataExtracted[$part1]);
                        if (isset($dataExtractedNested[$part2])) {
                            $rowExtracted[$col->getUniqueId()] = $dataExtractedNested[$part2];
                        }
                    }
                }
            }

            $dataPrepared[] = $rowExtracted;
        }

        $source = new SourceArray($dataPrepared);
        $source->setColumns($this->getColumns());
        $source->setSortConditions($this->getSortConditions());
        $source->setFilters($this->getFilters());
        $source->execute();

        $this->setPaginatorAdapter($source->getPaginatorAdapter());
    }
}
