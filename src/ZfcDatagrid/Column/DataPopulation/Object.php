<?php
namespace ZfcDatagrid\Column\DataPopulation;

use ZfcDatagrid\Column;

/**
 * Get the data from an external object
 */
class Object implements DataPopulationInterface
{

    /**
     *
     * @var ObjectAwareInterface
     */
    private $object;

    /**
     *
     * @var array
     */
    private $parameters = array();

    /**
     *
     * @param ObjectAwareInterface $object            
     * @throws \Exception
     */
    public function setObject(ObjectAwareInterface $object)
    {
        $this->object = $object;
    }

    /**
     *
     * @return ObjectAwareInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Apply a dynamic parameter based on row/column value
     * 
     * @param string $objectParameterName            
     * @param AbstractColumn $column            
     */
    public function addObjectParameterColumn($objectParameterName, Column\AbstractColumn $column)
    {
        $this->parameters[] = array(
            'objectParameterName' => $objectParameterName,
            'column' => $column
        );
    }

    /**
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Directly apply a "static" parameter
     *
     * @param string $name            
     * @param mixed $value            
     */
    public function setParameterValue($name, $value)
    {
        $this->getObject()->setParameterFromColumn($name, $value);
    }

    /**
     *
     * @return string
     */
    public function toString()
    {
        return $this->getObject()->toString();
    }
}
