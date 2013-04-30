<?php
namespace ZfcDatagrid;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
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
                    $dataGrid->setOptions($serviceManager->get('config')['ZfcDatagrid']);
                    $dataGrid->setMvcEvent($serviceManager->get('application')->getMvcEvent());
                    if ($serviceManager->has('translator') === true) {
                        $dataGrid->setTranslator($serviceManager->get('translator'));
                    }
                    $dataGrid->init();
                    
                    return $dataGrid;
                },
                
                'zfcDatagrid.renderer.bootstrapTable' => function  (ServiceManager $serviceManager)
                {
                    return new Renderer\Html\BootstrapTable();
                },
                
                'zfcDatagrid.renderer.printPlain' => function  (ServiceManager $serviceManager)
                {
                    return new Renderer\Html\PrintPlain();
                },
                
                'zfcDatagrid.renderer.tcpdf' => function  (ServiceManager $serviceManager)
                {
                    return new Renderer\Export\Tcpdf();
                },
                
                'zfcDatagrid.renderer.zendTable' => function  (ServiceManager $serviceManager)
                {
                    return new Renderer\Text\ZendTable();
                }
            )
        );
    }
    
    public function getConsoleUsage (Console $console)
    {
        return array(
            'show example grid' => 'Show example console ZfcDatagrid',
        );
    }
}
