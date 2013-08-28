<?php
namespace ZfcDatagrid;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ConsoleUsageProviderInterface
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
}
