<?php
namespace ZfcDatagrid;

use Zend\Console\Adapter\AdapterInterface as Console;

class Module
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php'
            ),

            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/../../config/module.config.php';
        $configNoCache = array(
            'ZfcDatagrid' => array(
                'renderer' => array(

                    'bootstrapTable' => array(
                        // Daterange bootstrapTable filter configuration example
                        'daterange' => array(
                            'enabled' => false,
                            'options' => array(
                                'ranges' => array(
                                    'Today' => new \Zend\Json\Expr("[moment().startOf('day'), moment().endOf('day')]"),
                                    'Yesterday' => new \Zend\Json\Expr("[moment().subtract('days', 1), moment().subtract('days', 1)]"),
                                    'Last 7 Days' => new \Zend\Json\Expr("[moment().subtract('days', 6), moment()]"),
                                    'Last 30 Days' => new \Zend\Json\Expr("[moment().subtract('days', 29), moment()]"),
                                    'This Month' => new \Zend\Json\Expr("[moment().startOf('month'), moment().endOf('month')]"),
                                    'Last Month' => new \Zend\Json\Expr("[moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]")
                                ),
                                'locale' => \Locale::getDefault(),
                                'format' => 'DD/MM/YY HH:mm:ss'
                            )
                        )
                    )
                )
            )
        );

        return array_merge_recursive($config, $configNoCache);
    }

    public function getServiceConfig()
    {
        if (class_exists('DoctrineORMModule\Service\DBALConnectionFactory')) {
            // For the doctrine examples!
            return array(
                'factories' => array(
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

        return array();
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
                '--items=NUMBER',
                'How much items to display per page [1...n]'
            ),
            array(
                '--sortBys=COLUMN',
                'Unique id of the column(s) to sort (split with: ,)'
            ),
            array(
                '--sortDirs=DIRECTION',
                'Sort direction of the columns [ASC|DESC] (split with: ,)'
            )
        );
    }
}
