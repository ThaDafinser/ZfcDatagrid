<?php
namespace ZfcDatagrid\Column\DataPopulation;

class StaticValue implements DataPopulationInterface
{
    private $value;

    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setObjectParameter($name, $value)
    {
        throw new \Exception('setObjectParameter() is not supported by this class');
    }

    public function getObjectParametersColumn()
    {
        return array();
    }

    public function toString()
    {
        return $this->getValue();
    }
}
