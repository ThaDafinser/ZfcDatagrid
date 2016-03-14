<?php

use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Renderer;
use ZfcDatagrid\Service;

return [
    'ZfcDatagrid' => [

        'settings' => [

            'default' => [
                // If no specific rendere given, use this renderes for HTTP / console
                'renderer' => [
                    'http'    => 'bootstrapTable',
                    'console' => 'zendTable',
                ],
            ],

            'export' => [
                // Export is enabled?
                'enabled' => true,

                'formats' => [],
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
                'mode' => 'direct',
            ],
        ],

        // The cache is used to save the filter + sort and other things for exporting
        'cache' => [

            'adapter' => [
                'name'    => 'Filesystem',
                'options' => [
                    'ttl'       => 720000, // cache with 200 hours,
                    'cache_dir' => 'data/ZfcDatagrid',
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],

                'Serializer',
            ],
        ],

        'renderer' => [

            'bootstrapTable' => [
                'parameterNames' => [
                    // Internal => bootstrapTable
                    'currentPage'    => 'currentPage',
                    'sortColumns'    => 'sortByColumns',
                    'sortDirections' => 'sortDirections',

                    'massIds' => 'ids',

                    'method' => 'POST',
                ],

                'daterange' => [
                    'enabled' => false,
                ],
            ],

            'jqGrid' => [
                'parameterNames' => [
                    // Internal => jqGrid
                    'currentPage'    => 'currentPage',
                    'itemsPerPage'   => 'itemsPerPage',
                    'sortColumns'    => 'sortByColumns',
                    'sortDirections' => 'sortDirections',
                    'isSearch'       => 'isSearch',

                    'massIds' => 'ids',

                    'method' => 'POST',
                ],
            ],

            'zendTable' => [
                'parameterNames' => [
                    // Internal => ZendTable (console)
                    'currentPage'    => 'page',
                    'itemsPerPage'   => 'items',
                    'sortColumns'    => 'sortBys',
                    'sortDirections' => 'sortDirs',

                    'filterColumns' => 'filterBys',
                    'filterValues'  => 'filterValues',
                ],
            ],

            'PHPExcel' => [

                'papersize' => 'A4',

                // landscape / portrait (we preferr landscape, because datagrids are often wide)
                'orientation' => 'landscape',

                // The worksheet name (will be translated if possible)
                'sheetName' => 'Data',

                // If you only want to export data, set this to false
                'displayTitle' => false,

                'rowTitle'     => 1,
                'startRowData' => 1,
            ],

            'TCPDF' => [

                'papersize' => 'A4',

                // landscape / portrait (we preferr landscape, because datagrids are often wide)
                'orientation' => 'landscape',

                'margins' => [
                    'header' => 5,
                    'footer' => 10,

                    'top'    => 20,
                    'bottom' => 11,
                    'left'   => 10,
                    'right'  => 10,
                ],

                'icon' => [
                    // milimeter...
                    'size' => 16,
                ],

                'header' => [
                    // define your logo here, please be aware of the relative path...
                    'logo'      => '',
                    'logoWidth' => 35,
                ],

                'style' => [

                    'header' => [
                        'font' => 'helvetica',
                        'size' => 11,

                        'color' => [
                            0,
                            0,
                            0,
                        ],
                        'background-color' => [
                            255,
                            255,
                            200,
                        ],
                    ],

                    'data' => [
                        'font' => 'helvetica',
                        'size' => 11,

                        'color' => [
                            0,
                            0,
                            0,
                        ],
                        'background-color' => [
                            255,
                            255,
                            255,
                        ],
                    ],
                ],
            ],

            'csv' => [
                // draw a header with all column labels?
                'header'    => true,
                'delimiter' => ',',
                'enclosure' => '"',
            ],
        ]
        ,

        // General parameters
        'generalParameterNames' => [
            'rendererType' => 'rendererType',
        ],
    ],

    'service_manager' => [

        'factories' => [
            Service\DatagridManager::class => Service\DatagridManagerFactory::class,
            Datagrid::class => Service\DatagridFactory::class,

            'zfcDatagrid_dbAdapter' => Service\ZendDbAdapterFactory::class,

            // HTML renderer
            Renderer\BootstrapTable\Renderer::class => function() {
                return new Renderer\BootstrapTable\Renderer();
            },
            Renderer\JqGrid\Renderer::class => function() {
                return new Renderer\JqGrid\Renderer();
            },

            // CLI renderer
            Renderer\ZendTable\Renderer::class => function() {
                return new Renderer\ZendTable\Renderer();
            },

            // Export renderer
            Renderer\PrintHtml\Renderer::class => function() {
                return new Renderer\PrintHtml\Renderer();
            },
            Renderer\PHPExcel\Renderer::class => function() {
                return new Renderer\PHPExcel\Renderer();
            },
            Renderer\TCPDF\Renderer::class => function() {
                return new Renderer\TCPDF\Renderer();
            },
            Renderer\Csv\Renderer::class => function() {
                return new Renderer\Csv\Renderer();
            },
        ],

        'aliases' => [
            'zfcDatagrid' => Datagrid::class,
            'ZfcDatagridManager' => Service\DatagridManager::class,

            // HTML renderer
            'zfcDatagrid.renderer.bootstrapTable' => Renderer\BootstrapTable\Renderer::class,
            'zfcDatagrid.renderer.jqgrid'         => Renderer\JqGrid\Renderer::class,

            // CLI renderer
            'zfcDatagrid.renderer.zendTable' => Renderer\ZendTable\Renderer::class,

            // Export renderer
            'zfcDatagrid.renderer.printHtml' => Renderer\PrintHtml\Renderer::class,
            'zfcDatagrid.renderer.PHPExcel'  => Renderer\PHPExcel\Renderer::class,
            'zfcDatagrid.renderer.TCPDF'     => Renderer\TCPDF\Renderer::class,
            'zfcDatagrid.renderer.csv'       => Renderer\Csv\Renderer::class,
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'bootstrapTableRow' => Renderer\BootstrapTable\View\Helper\TableRow::class,
            'jqgridColumns'     => Renderer\JqGrid\View\Helper\Columns::class,
        ],
        'factories' => [
            Renderer\BootstrapTable\View\Helper\TableRow::class => function($sm) {
                /** @var $sm \Zend\ServiceManager\ServiceLocatorInterface */
                $tableRow = new Renderer\BootstrapTable\View\Helper\TableRow();
                if($sm->has('translator')){
                    /** @noinspection PhpParamsInspection */
                    $tableRow->setTranslator($sm->get('translator'));
                }

                return $tableRow;
            },
            Renderer\JqGrid\View\Helper\Columns::class => function($sm) {
                /** @var $sm \Zend\ServiceManager\ServiceLocatorInterface */
                $tableRow = new Renderer\JqGrid\View\Helper\Columns();
                if($sm->has('translator')){
                    /** @noinspection PhpParamsInspection */
                    $tableRow->setTranslator($sm->get('translator'));
                }

                return $tableRow;
            },
        ],
    ],

    'view_manager' => [

        'strategies' => [
            'ViewJsonStrategy',
        ],

        'template_map' => [
            'zfc-datagrid/renderer/bootstrapTable/layout' => __DIR__ . '/../view/zfc-datagrid/renderer/bootstrapTable/layout.phtml',
            'zfc-datagrid/renderer/printHtml/layout'      => __DIR__ . '/../view/zfc-datagrid/renderer/printHtml/layout.phtml',
            'zfc-datagrid/renderer/printHtml/table'       => __DIR__ . '/../view/zfc-datagrid/renderer/printHtml/table.phtml',
            'zfc-datagrid/renderer/jqGrid/layout'         => __DIR__ . '/../view/zfc-datagrid/renderer/jqGrid/layout.phtml',
        ],

        'template_path_stack' => [
            'ZfcDatagrid' => __DIR__ . '/../view',
        ],
    ],
];
