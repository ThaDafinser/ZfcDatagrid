<?php
namespace ZfcDatagrid\DataSource;

use ZfcDatagrid\DataSource\PhpArray as SourceArray;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class Doctrine2Collection extends AbstractDataSource
{

    /**
     *
     * @var Collection
     */
    private $data;

    /**
     *
     * @var EntityManager
     */
    private $em;

    /**
     * Data source
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
                $return = get_class($return);
            }
            throw new \InvalidArgumentException("Unknown data input..." . $return);
        }
    }

    /**
     *
     * @return Collection
     */
    public function getData()
    {
        return $this->data;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    public function execute()
    {
        $hydrator = new DoctrineHydrator($this->getEntityManager());
        
        $dataPrepared = array();
        foreach ($this->getData() as $row) {
            $dataExtracted = $hydrator->extract($row);
            
            $rowExtracted = array();
            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                $part1 = $column->getSelectPart1();
                $part2 = $column->getSelectPart2();
                
                if ($part2 === null) {
                    if (isset($dataExtracted[$part1])) {
                        $rowExtracted[$column->getUniqueId()] = $dataExtracted[$part1];
                    }
                } else {
                    if (isset($dataExtracted[$part2][$part1])) {
                        $rowExtracted[$column->getUniqueId()] = $dataExtracted[$part2][$part1];
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
