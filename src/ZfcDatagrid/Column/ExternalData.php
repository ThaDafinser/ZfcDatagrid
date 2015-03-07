<?php
namespace ZfcDatagrid\Column;

class ExternalData extends AbstractColumn
{
    /**
     *
     * @var DataPopulation\DataPopulationInterface
     */
    protected $dataPopulation;

    public function __construct($uniqueId = 'external')
    {
        $this->setUniqueId($uniqueId);

        $this->setUserSortDisabled(true);
        $this->setUserFilterDisabled(true);
    }

    /**
     *
     * @param DataPopulation\DataPopulationInterface $dataPopulation
     */
    public function setDataPopulation(DataPopulation\DataPopulationInterface $dataPopulation)
    {
        if ($dataPopulation instanceof DataPopulation\Object && $dataPopulation->getObject() === null) {
            throw new \Exception('object is missing in DataPopulation\Object!');
        }

        $this->dataPopulation = $dataPopulation;
    }

    /**
     *
     * @return DataPopulation\DataPopulationInterface
     */
    public function getDataPopulation()
    {
        if (null === $this->dataPopulation) {
            throw new \InvalidArgumentException('no data population set for Column\ExternalData');
        }

        return $this->dataPopulation;
    }

    /**
     *
     * @return boolean
     */
    public function hasDataPopulation()
    {
        if ($this->dataPopulation !== null) {
            return true;
        }

        return false;
    }
}
