<?php
/**
 * Copy this configuration file info config/autoload/zfcdatagrid.local.php
 * Then it will override the default settings and you can use your own!
 */
return array(
    'ZfcDatagrid' => array(
        
        'settings' => array(
            
            'default' => array(
                'renderer' => array(
                    //http => jqGrid,
                    'http' => 'bootstrapTable',
                    'console' => 'zendTable'
                )
            ),
            
            'export' => array(
                'enabled' => true,
                
                //currently only A formats are supported...
                'papersize' => 'A4',
                
                // landscape / portrait (we preferr landscape, because datagrids are often wide)
                'orientation' => 'landscape',
                
                'formats' => array(
                    //renderer -> display Name (can also be HTML)
                    'PHPExcel' => 'Excel',
                    'tcpdf' => 'PDF'
                ),
                
                // The output+save directory
                'path' => 'public/download',
                
                'mode' => 'direct'
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
            'jqGrid' => array(
                'templates' => array(
                    'layout' => 'zfc-datagrid/renderer/jqGrid/layout'
                )
            )
        ),
        
        
    )
);