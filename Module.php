<?php
namespace ZfcDatagrid;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcDatagrid\Datagrid;

class Module implements AutoloaderProviderInterface
{

    public function getAutoloaderConfig ()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getConfig ()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig ()
    {
        return array(
            'factories' => array(
                'zfcDatagrid' => function  (ServiceManager $serviceManager)
                {
                    $dataGrid = new Datagrid();
                    $dataGrid->setRequest($serviceManager->get('request'));
                    $dataGrid->setResponse($serviceManager->get('response'));
                    if ($serviceManager->has('translator') === true) {
                        $dataGrid->setTranslator($serviceManager->get('translator'));
                    }
                    
                    return $dataGrid;
                }
            )
        );
    }
}
