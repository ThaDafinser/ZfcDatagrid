<?php
namespace ZfcDatagridTest\Util;

use Zend\Mvc\Service\ServiceListenerFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ServiceManagerFactory
 * @package ZfcDatagridTest\Util
 */
class ServiceManagerFactory
{
    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        static::$config = $config;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public static function getServiceManager()
    {
        $serviceManager = new ServiceManager(
            isset(static::$config['service_manager']) ? static::$config['service_manager'] : []
        );
        $serviceManager->setService('Applicationconfig', static::$config);
        $serviceManager->setFactory('ServiceListener', ServiceListenerFactory::class);

        $serviceManager->setService('config', self::$config);

        return $serviceManager;
    }
}
