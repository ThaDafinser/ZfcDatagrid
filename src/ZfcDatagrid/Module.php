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
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getServiceConfig ()
    {
        return array(
            
            'factories' => array(
                'zfcDatagrid' => function  (ServiceManager $serviceManager)
                {
                    $dataGrid = new Datagrid();
                    $dataGrid->setOptions($serviceManager->get('config')['ZfcDatagrid']);
                    $dataGrid->setMvcEvent($serviceManager->get('application')
                        ->getMvcEvent());
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
                },
                
                'zfcDatagrid.examples.data.phpArray' => function  (ServiceManager $serviceManager)
                {
                    return new Examples\Data\PhpArray();
                },
                
                'zfcDatagrid.examples.data.doctrine2' => function  (ServiceManager $serviceManager)
                {
                    return new Examples\Data\Doctrine2();
                },
                
                'zfcDatagrid.examples.data.zendSelect' => function  (ServiceManager $serviceManager)
                {
                    return new Examples\Data\ZendSelect();
                },
                
                'zfcDatagrid_dbAdapter' => function(ServiceManager $serviceManager){
                    return new \Zend\Db\Adapter\Adapter($serviceManager->get('config')['zfcDatagrid_dbAdapter']);
                },
                
                // For the doctrine examples!
                'doctrine.connection.orm_zfcDatagrid' => new \DoctrineORMModule\Service\DBALConnectionFactory('orm_zfcDatagrid'),
                'doctrine.configuration.orm_zfcDatagrid' => new \DoctrineORMModule\Service\ConfigurationFactory('orm_zfcDatagrid'),
                'doctrine.entitymanager.orm_zfcDatagrid' => new \DoctrineORMModule\Service\EntityManagerFactory('orm_zfcDatagrid'),
                
                'doctrine.driver.orm_zfcDatagrid' => new \DoctrineModule\Service\DriverFactory('orm_zfcDatagrid'),
                'doctrine.eventmanager.orm_zfcDatagrid' => new \DoctrineModule\Service\EventManagerFactory('orm_zfcDatagrid'),
                'doctrine.entity_resolver.orm_zfcDatagrid' => new \DoctrineORMModule\Service\EntityResolverFactory('orm_zfcDatagrid'),
                'doctrine.sql_logger_collector.orm_zfcDatagrid' => new \DoctrineORMModule\Service\SQLLoggerCollectorFactory('orm_zfcDatagrid')
            )
        );
    }

    public function getConsoleUsage (Console $console)
    {
        return array(
            'datagrid person' => 'Show example person ZfcDatagrid'
        );
    }
}
