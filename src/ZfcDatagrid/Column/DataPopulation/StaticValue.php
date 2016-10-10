<?php

namespace ZfcDatagrid\Column\DataPopulation;

class StaticValue implements DataPopulationInterface
{
    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws \Exception
     */
    public function setObjectParameter($name, $value)
    {
        throw new \Exception('setObjectParameter() is not supported by this class');
    }

    /**
     * @return array
     */
    public function getObjectParametersColumn()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function toString()
    {
        return $this->getValue();
    }
}
