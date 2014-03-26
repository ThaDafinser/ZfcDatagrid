# Configuration of ZfcDatagrid

There are a lot of configurations you can override to customize the behaviour of your grid implementation.

You can find all options here (all options inside "ZfcDatagrid")
https://github.com/ThaDafinser/ZfcDatagrid/blob/master/config/module.config.php

To override the configuration you should create a own config file for this module.
Create: config/autoload/zfcdatagrid.local.php

Following configs are currently available (taken from config/module.config.php):
```PHP
return array(
    'ZfcDatagrid' => array(
        
        'defaults' => array(
            // If no specific rendere given, use this renderes for HTTP / console
            'renderer' => array(
                'http' => 'bootstrapTable',
                'console' => 'zendTable'
            ),
            
            // general available export formats
            'export' => array(
                'tcpdf' => 'PDF',
                'phpExcel' => 'Excel'
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
                    'sortDirections' => 'sortDirections'
                ),
                // Set it if you want daterange picker on your bootstrap table
                'daterange' => array(
                    'enabled' => true,
                    'options' => array(
                        'ranges' => array(
                            'Today' => new \Zend\Json\Expr("[moment().startOf('day'), moment().endOf('day')]"),
                            'Yesterday' => new \Zend\Json\Expr("[moment().subtract('days', 1), moment().subtract('days', 1)]"), 
                            'Last 7 Days' => new \Zend\Json\Expr("[moment().subtract('days', 6), moment()]"),
                            'Last 30 Days'=> new \Zend\Json\Expr("[moment().subtract('days', 29), moment()]"),
                            'This Month' => new \Zend\Json\Expr("[moment().startOf('month'), moment().endOf('month')]"),
                            'Last Month' => new \Zend\Json\Expr("[moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]"),
                        ),
                        'locale' => \Locale::getDefault(),
                        'format' => 'DD/MM/YY HH:mm:ss',
                    ),
                ),
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
                    'columnsGroupByLocal' => 'columnsGroupBy'
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
            )
        ),
        
        // General parameters
        'generalParameterNames' => array(
            'rendererType' => 'rendererType'
        )
    )
);
```
