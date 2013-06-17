<?php
namespace ZfcDatagrid\ServiceManager;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatagridManagerFactory extends AbstractPluginManagerFactory
{

    const PLUGIN_MANAGER_CLASS = 'ZfcDatagrid\ServiceManager\DatagridManager';

    /**
     * Create and return the MVC controller plugin manager
     *
     * @param ServiceLocatorInterface $serviceLocator            
     * @return FormElementManager
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);
        $plugins->addPeeringServiceManager($serviceLocator);
        $plugins->setRetrieveFromPeeringManagerFirst(true);
        return $plugins;
    }
}
