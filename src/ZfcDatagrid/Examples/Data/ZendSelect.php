<?php
namespace ZfcDatagrid\Examples\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Select;

class ZendSelect implements ServiceLocatorAwareInterface
{

    private $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getPersons()
    {
        $select = new Select();
        $select->from(array(
            'p' => 'person'
        ));
        $select->join(array(
            'g' => 'group'
        ), 'g.id = p.primaryGroupId', 'name', 'left');

        return $select;
    }
}
