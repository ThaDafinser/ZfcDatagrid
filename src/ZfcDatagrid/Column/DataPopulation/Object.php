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

    private $parameters = array();

    public function __construct ()
    {}

    /**
     *
     * @param ObjectAwareInterface $object            
     * @throws \Exception
     */
    public function setObject ($object)
    {
        if (! $object instanceof ObjectAwareInterface) {
            throw new \Exception('The provided object must implement the interfae "ZfcDatagrid\Column\DataPopulation\ObjectAwareInterface"');
        }
        
        $this->object = $object;
    }

    /**
     *
     * @return mixed
     */
    public function getObject ()
    {
        return $this->object;
    }

    /**
     *
     * @param string $objectParameterName            
     * @param AbstractColumn $column            
     */
    public function addObjectParameterColumn ($objectParameterName, Column\AbstractColumn $column)
    {
        $this->parameters[] = array('objectParameterName' => $objectParameterName, 'column' => $column);
    }

    public function getParameters ()
    {
        return $this->parameters;
    }

    public function setParameterValue ($name, $value)
    {
        $this->getObject()->setParameterFromColumn($name, $value);
    }

    /**
     *
     * @return string
     */
    public function toString ()
    {
        return $this->getObject ()->toString();
    }
}
