<?php

namespace ZfcDatagrid\Service;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for form elements.
 *
 * Enforces that elements retrieved are instances of ElementInterface.
 */
class DatagridManager extends AbstractPluginManager
{
    /**
     * Don't share form elements by default.
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @param mixed $plugin
     */
    public function validatePlugin($plugin)
    {
    }

    /**
     * Retrieve a service from the manager by name.
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param string       $name
     * @param string|array $options
     * @param bool         $usePeeringServiceManagers
     *
     * @return object
     */
    public function get($name, $options = [], $usePeeringServiceManagers = true)
    {
        /* @var $instance \LispBase\ZfcDatagrid\Datagrid */
        $instance = new $name();
        $instance->createService($this->getServiceLocator());

        return $instance;
    }
}
