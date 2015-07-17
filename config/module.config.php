<?php
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

        'invokables' => [

            // HTML renderer
            'zfcDatagrid.renderer.bootstrapTable' => 'ZfcDatagrid\Renderer\BootstrapTable\Renderer',
            'zfcDatagrid.renderer.jqgrid'         => 'ZfcDatagrid\Renderer\JqGrid\Renderer',

            // CLI renderer
            'zfcDatagrid.renderer.zendTable' => 'ZfcDatagrid\Renderer\ZendTable\Renderer',

            // Export renderer
            'zfcDatagrid.renderer.printHtml' => 'ZfcDatagrid\Renderer\PrintHtml\Renderer',
            'zfcDatagrid.renderer.PHPExcel'  => 'ZfcDatagrid\Renderer\PHPExcel\Renderer',
            'zfcDatagrid.renderer.TCPDF'     => 'ZfcDatagrid\Renderer\TCPDF\Renderer',
            'zfcDatagrid.renderer.csv'       => 'ZfcDatagrid\Renderer\Csv\Renderer',
        ],

        'factories' => [
            'ZfcDatagridManager'   => 'ZfcDatagrid\Service\DatagridManagerFactory',
            'ZfcDatagrid\Datagrid' => 'ZfcDatagrid\Service\DatagridFactory',

            'zfcDatagrid_dbAdapter' => 'ZfcDatagrid\Service\ZendDbAdapterFactory',
        ],

        'aliases' => [
            'zfcDatagrid' => 'ZfcDatagrid\Datagrid',
        ],
    ],

    'view_helpers' => [
        'invokables' => [
            'bootstrapTableRow' => 'ZfcDatagrid\Renderer\BootstrapTable\View\Helper\TableRow',
            'jqgridColumns'     => 'ZfcDatagrid\Renderer\JqGrid\View\Helper\Columns',
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
