<?php
use Zend\ServiceManager\ServiceManager;

return array(
    'ZfcDatagrid' => array(
        
        'defaults' => array(
            'renderer' => array(
                'http' => 'bootstrapTable',
                'console' => 'zendTable'
            )
        ),
        
        'cache' => array(
            'adapter' => array(
                'name' => 'Filesystem',
                'options' => array(
                    'ttl' => 720000, // cache with 200 hours,
                    'cache_dir' => 'data/ZfcDatagrid'
                )
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false
                )
            )
        ),
        
        'enabledExportFormats' => array(
            'printPlain'
        ),
        
        'parameters' => array(
            'currentPage' => 'page',
            'sortColumn' => 'sortByColumn',
            'sortDirection' => 'sortDirection',
            
            'rendererType' => 'rendererType'
        )
    ),
    
    'service_manager' => array(
        'invokables' => array(
            'zfcDatagrid.renderer.bootstrapTable' => 'ZfcDatagrid\Renderer\Html\BootstrapTable',
            'zfcDatagrid.renderer.printPlain' => 'ZfcDatagrid\Renderer\Html\PrintPlain',
            'zfcDatagrid.renderer.zendTable' => 'ZfcDatagrid\Renderer\Text\ZendTable',
            
            'zfcDatagrid.examples.data.phpArray' => 'ZfcDatagrid\Examples\Data\PhpArray',
            'zfcDatagrid.examples.data.doctrine2' => 'ZfcDatagrid\Examples\Data\Doctrine2',
            'zfcDatagrid.examples.data.zendSelect' => 'ZfcDatagrid\Examples\Data\ZendSelect',
        ),
        
        'factories' => array(
            'zfcDatagrid' => function  (ServiceManager $serviceManager)
            {
                $dataGrid = new \ZfcDatagrid\Datagrid();
                $dataGrid->setOptions($serviceManager->get('config')['ZfcDatagrid']);
                $dataGrid->setMvcEvent($serviceManager->get('application')
                    ->getMvcEvent());
                if ($serviceManager->has('translator') === true) {
                    $dataGrid->setTranslator($serviceManager->get('translator'));
                }
                $dataGrid->init();
        
                return $dataGrid;
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
    ),
    

    'view_manager' => array(
    
        'template_map' => array(
            'zfc-datagrid/renderer/html/bootstrap-table' => __DIR__ . '/../view/zfc-datagrid/renderer/html/bootstrap-table.phtml',
            'zfc-datagrid/renderer/html/print-plain' => __DIR__ . '/../view/zfc-datagrid/renderer/html/print-plain.phtml'
        ),
    
        'template_path_stack' => array(
            'ZfcDatagrid' => __DIR__ . '/../view'
        )
    ),
    
    
    /**
     * ONLY EXAMPLE CONFIGURATION BELOW!!!!!!
     */
    'controllers' => array(
        'invokables' => array(
            'ZfcDatagrid\Examples\Controller\Person' => 'ZfcDatagrid\Examples\Controller\PersonController',
            'ZfcDatagrid\Examples\Controller\PersonDoctrine2' => 'ZfcDatagrid\Examples\Controller\PersonDoctrine2Controller',
            'ZfcDatagrid\Examples\Controller\PersonZend' => 'ZfcDatagrid\Examples\Controller\PersonZendController'
        )
    ),
    
    'router' => array(
        'routes' => array(
            'ZfcDatagrid' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/zfcDatagrid',
                    'defaults' => array(
                        '__NAMESPACE__' => 'ZfcDatagrid\Examples\Controller',
                        'controller' => 'person',
                        'action' => 'bootstrap'
                    )
                ),
                
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array()
                        ),
                        
                        'may_terminate' => true,
                        'child_routes' => array(
                            'wildcard' => array(
                                'type' => 'Wildcard',
                                
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'wildcard' => array(
                                        'type' => 'Wildcard'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    ),
    
    'console' => array(
        'router' => array(
            'routes' => array(
                'ZfcDatagrid' => array(
                    'options' => array(
                        'route' => 'datagrid person [--page=]',
                        'defaults' => array(
                            'controller' => 'ZfcDatagrid\Examples\Controller\Person',
                            'action' => 'console'
                        )
                    )
                )
            )
        )
    ),
    
    /**
     * The ZF2 DbAdapter + Doctrine2 connection is must for examples!
     */
    'zfcDatagrid_dbAdapter' => array(
        'driver' => 'Pdo_Sqlite',
        'database' => 'data/ZfcDatagrid/testDb.sqlite'
    ),
    
    'doctrine' => array(
        'connection' => array(
            'orm_zfcDatagrid' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'charset' => 'utf8',
                    'path' => 'data/ZfcDatagrid/testDb.sqlite'
                )
            )
        ),
        
        'configuration' => array(
            'orm_zfcDatagrid' => array(
                'metadata_cache' => 'array',
                'query_cache' => 'array',
                'result_cache' => 'array',
                'driver' => 'orm_zfcDatagrid',
                'generate_proxies' => true,
                'proxy_dir' => 'data/ZfcDatagrid/Proxy',
                'proxy_namespace' => 'ZfcDatagrid\Proxy',
                'filters' => array()
            )
        ),
        
        'driver' => array(
            'ZfcDatagrid_Driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/ZfcDatagrid/Examples/Entity'
                )
            ),
            
            'orm_zfcDatagrid' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    'ZfcDatagrid\Examples\Entity' => 'ZfcDatagrid_Driver'
                )
            )
        ),
        
        // now you define the entity manager configuration
        'entitymanager' => array(
            // This is the alternative config
            'orm_zfcDatagrid' => array(
                'connection' => 'orm_zfcDatagrid',
                'configuration' => 'orm_zfcDatagrid'
            )
        ),
        
        'eventmanager' => array(
            'orm_crawler' => array()
        ),
        
        'sql_logger_collector' => array(
            'orm_crawler' => array()
        ),
        
        'entity_resolver' => array(
            'orm_crawler' => array()
        )
    )
);
