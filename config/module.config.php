<?php
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
            'plainText'
        ),
        
        'parameters' => array(
            'currentPage' => 'page',
            'sortColumn' => 'sortByColumn',
            'sortDirection' => 'sortDirection',
            
            'rendererType' => 'rendererType'
        )
    ),
    
    /**
     * Controller + routing for examples
     */
    'controllers' => array(
        'invokables' => array(
            'ZfcDatagrid\Controller\Example' => 'ZfcDatagrid\Controller\ExampleController'
        )
    ),

    'router' => array(
        'routes' => array(
            'ZfcDatagrid' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/zfcDatagrid',
                    'defaults' => array(
                        '__NAMESPACE__' => 'ZfcDatagrid\Controller',
                        'controller' => 'Example',
                        'action' => 'index'
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
                        'route' => 'show example grid [--page=]',
                        'defaults' => array(
                            'controller' => 'ZfcDatagrid\Controller\Example',
                            'action' => 'console'
                        )
                    )
                )
            )
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
    )
);
