<?php
return array(
    
    'ZfcDatagrid' => array(
        
        'settings' => array(
            
            'default' => array(
                // If no specific rendere given, use this renderes for HTTP / console
                'renderer' => array(
                    'http' => 'bootstrapTable',
                    'console' => 'zendTable'
                )
            ),
            
            'export' => array(
                // Export is enabled?
                'enabled' => true,
                
                'formats' => array(),
                // type => Displayname (Toolbar - you can use here HTML too...)
                // 'printHtml' => 'Print',
                // 'tcpdf' => 'PDF',
                // 'csv' => 'CSV',
                // 'PHPExcel' => 'Excel',
                
                // The output+save directory
                'path' => 'data/ZfcDatagrid',
                
                // mode can be:
                // direct = PHP handles header + file reading
                // @TODO iframe = PHP generates the file and a hidden <iframe> sends the document (ATTENTION: your webserver must enable "force-download" for excel/pdf/...)
                'mode' => 'direct'
            )
        ),
        
        // The cache is used to save the filter + sort and other things for exporting
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
                ),
                
                'Serializer'
            )
        ),
        
        'renderer' => array(
            
            'bootstrapTable' => array(
                'parameterNames' => array(
                    // Internal => bootstrapTable
                    'currentPage' => 'currentPage',
                    'sortColumns' => 'sortByColumns',
                    'sortDirections' => 'sortDirections',
                    
                    'massIds' => 'ids'
                )
            ),
            
            'jqGrid' => array(
                'parameterNames' => array(
                    // Internal => jqGrid
                    'currentPage' => 'currentPage',
                    'itemsPerPage' => 'itemsPerPage',
                    'sortColumns' => 'sortByColumns',
                    'sortDirections' => 'sortDirections',
                    'isSearch' => 'isSearch',
                    
                    'columnsHidden' => 'columnsHidden',
                    'columnsGroupByLocal' => 'columnsGroupBy',
                    
                    'massIds' => 'ids'
                )
            ),
            
            'zendTable' => array(
                'parameterNames' => array(
                    // Internal => ZendTable (console)
                    'currentPage' => 'page',
                    'itemsPerPage' => 'items',
                    'sortColumns' => 'sortBys',
                    'sortDirections' => 'sortDirs',
                    
                    'filterColumns' => 'filterBys',
                    'filterValues' => 'filterValues'
                )
            ),
            
            'PHPExcel' => array(
                
                'papersize' => 'A4',
                
                // landscape / portrait (we preferr landscape, because datagrids are often wide)
                'orientation' => 'landscape',
                
                // The worksheet name (will be translated if possible)
                'sheetName' => 'Data',
                
                // If you only want to export data, set this to false
                'displayTitle' => true,
                
                'rowTitle' => 1,
                'startRowData' => 3
            ),
            
            'TCPDF' => array(
                
                'papersize' => 'A4',
                
                // landscape / portrait (we preferr landscape, because datagrids are often wide)
                'orientation' => 'landscape',
                
                'margins' => array(
                    'header' => 5,
                    'footer' => 10,
                    
                    'top' => 20,
                    'bottom' => 11,
                    'left' => 10,
                    'right' => 10
                ),
                
                'icon' => array(
                    // milimeter...
                    'size' => 16
                ),
                
                'header' => array(
                    // define your logo here, please be aware of the relative path...
                    'logo' => '',
                    'logoWidth' => 35
                ),
                
                'style' => array(
                    
                    'header' => array(
                        'font' => 'helvetica',
                        'size' => 11,
                        
                        'color' => array(
                            0,
                            0,
                            0
                        ),
                        'background-color' => array(
                            255,
                            255,
                            200
                        )
                    ),
                    
                    'data' => array(
                        'font' => 'helvetica',
                        'size' => 11,
                        
                        'color' => array(
                            0,
                            0,
                            0
                        ),
                        'background-color' => array(
                            255,
                            255,
                            255
                        )
                    )
                )
            ),
            
            'csv' => array(
                // draw a header with all column labels?
                'header' => true,
                'delimiter' => ',',
                'enclosure' => '"'
            )
        )
        ,
        
        // General parameters
        'generalParameterNames' => array(
            'rendererType' => 'rendererType'
        )
    ),
    
    'service_manager' => array(
        
        'invokables' => array(
            
            // HTML renderer
            'zfcDatagrid.renderer.bootstrapTable' => 'ZfcDatagrid\Renderer\BootstrapTable\Renderer',
            'zfcDatagrid.renderer.jqgrid' => 'ZfcDatagrid\Renderer\JqGrid\Renderer',
            
            // CLI renderer
            'zfcDatagrid.renderer.zendTable' => 'ZfcDatagrid\Renderer\ZendTable\Renderer',
            
            // Export renderer
            'zfcDatagrid.renderer.printHtml' => 'ZfcDatagrid\Renderer\PrintHtml\Renderer',
            'zfcDatagrid.renderer.PHPExcel' => 'ZfcDatagrid\Renderer\PHPExcel\Renderer',
            'zfcDatagrid.renderer.TCPDF' => 'ZfcDatagrid\Renderer\TCPDF\Renderer',
            'zfcDatagrid.renderer.csv' => 'ZfcDatagrid\Renderer\Csv\Renderer',
            
            // Datasources
            'zfcDatagrid.examples.data.phpArray'        => 'ZfcDatagrid\Examples\Data\PhpArray',
            'zfcDatagrid.examples.data.doctrine2'       => 'ZfcDatagrid\Examples\Data\Doctrine2',
            'zfcDatagrid.examples.data.zendSelect'      => 'ZfcDatagrid\Examples\Data\ZendSelect',
        	'zfcDatagrid.examples.data.jqgrid.phpArray' => 'ZfcDatagrid\Examples\Data\JqGrid\PhpArray',
        ),
        
        'factories' => array(
            'ZfcDatagrid\Datagrid' => 'ZfcDatagrid\Service\DatagridFactory',
            
            'zfcDatagrid_dbAdapter' => 'ZfcDatagrid\Service\ZendDbAdapterFactory'
        ),
        
        'aliases' => array(
            'zfcDatagrid' => 'ZfcDatagrid\Datagrid'
        )
    ),
    
    'view_helpers' => array(
        'invokables' => array(
            'bootstrapTableRow' => 'ZfcDatagrid\Renderer\BootstrapTable\View\Helper\TableRow',
            'jqgridColumns' => 'ZfcDatagrid\Renderer\JqGrid\View\Helper\Columns'
        )
    ),
    
    'view_manager' => array(
        
        'strategies' => array(
            'ViewJsonStrategy'
        ),
        
        'template_map' => array(
            'zfc-datagrid/renderer/bootstrapTable/layout'      => __DIR__ . '/../view/zfc-datagrid/renderer/bootstrapTable/layout.phtml',
            'zfc-datagrid/renderer/printHtml/layout'           => __DIR__ . '/../view/zfc-datagrid/renderer/printHtml/layout.phtml',
            'zfc-datagrid/renderer/printHtml/table'            => __DIR__ . '/../view/zfc-datagrid/renderer/printHtml/table.phtml',
            'zfc-datagrid/renderer/jqGrid/layout'              => __DIR__ . '/../view/zfc-datagrid/renderer/jqGrid/layout.phtml',
        	'zfc-datagrid/renderer/jqGrid/layout-groupingview' => __DIR__ . '/../view/zfc-datagrid/renderer/jqGrid/layout-groupingview.phtml'
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
            'ZfcDatagrid\Examples\Controller\Person'          => 'ZfcDatagrid\Examples\Controller\PersonController',
            'ZfcDatagrid\Examples\Controller\PersonDoctrine2' => 'ZfcDatagrid\Examples\Controller\PersonDoctrine2Controller',
            'ZfcDatagrid\Examples\Controller\PersonZend'      => 'ZfcDatagrid\Examples\Controller\PersonZendController',
            'ZfcDatagrid\Examples\Controller\Minimal'         => 'ZfcDatagrid\Examples\Controller\MinimalController',
            'ZfcDatagrid\Examples\Controller\Category'        => 'ZfcDatagrid\Examples\Controller\CategoryController',
        	'ZfcDatagrid\Examples\Controller\JqGrid'          => 'ZfcDatagrid\Examples\Controller\JqGridController',
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
                'datagrid-person' => array(
                    'options' => array(
                        'route' => 'datagrid person [--page=] [--items=] [--filterBys=] [--filterValues=] [--sortBys=] [--sortDirs=]',
                        'defaults' => array(
                            'controller' => 'ZfcDatagrid\Examples\Controller\Person',
                            'action' => 'console'
                        )
                    )
                ),
                
                'datagrid-category' => array(
                    'options' => array(
                        'route' => 'datagrid category [--page=] [--items=] [--filterBys=] [--filterValues=] [--sortBys=] [--sortDirs=]',
                        'defaults' => array(
                            'controller' => 'ZfcDatagrid\Examples\Controller\Category',
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
        'database' => __DIR__ . '/../src/ZfcDatagrid/Examples/Data/examples.sqlite'
    ),
    
    'doctrine' => array(
        'connection' => array(
            'orm_zfcDatagrid' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'charset' => 'utf8',
                    'path' => __DIR__ . '/../src/ZfcDatagrid/Examples/Data/examples.sqlite'
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
