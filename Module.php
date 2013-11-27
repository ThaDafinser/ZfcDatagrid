<?php
namespace ZfcDatagrid;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ServiceManager\ServiceManager;

class Module implements 
    AutoloaderProviderInterface, 
    ConfigProviderInterface, 
    ConsoleUsageProviderInterface, 
    ServiceProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getConsoleUsage(Console $console)
    {
        return array(
            'Display the example console datagrid',
            'datagrid person' => 'Show person datagrid',
            'datagrid category' => 'Show category datagrid',
            
            'Options:',
            array(
                '--page=NUMBER',
                'Number of the page to display [1...n]'
            ),
            array(
                '--itmes=NUMBER',
                'How much items to display per page [1...n]'
            ),
            array(
                '--sortBy=COLUMN',
                'Unique id of the column(s) to sort (split with: ,)'
            ),
            array(
                '--sortDir=DIRECTION',
                'Sort direction of the columns [ASC|DESC] (split with: ,)'
            )
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                
                'zfcDatagrid' => function  (ServiceManager $serviceManager)
                {
                    $config = $serviceManager->get('config');
                    $dataGrid = new \ZfcDatagrid\Datagrid();
                    $dataGrid->setOptions($config['ZfcDatagrid']);
                    $dataGrid->setMvcEvent($serviceManager->get('application')
                        ->getMvcEvent());
                    if ($serviceManager->has('translator') === true) {
                        $dataGrid->setTranslator($serviceManager->get('translator'));
                    }
                    $dataGrid->init();
                    
                    return $dataGrid;
                },
                
                'zfcDatagrid_dbAdapter' => function  (ServiceManager $serviceManager)
                {
                    $config = $serviceManager->get('config');
                    return new \Zend\Db\Adapter\Adapter($config['zfcDatagrid_dbAdapter']);
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
}
