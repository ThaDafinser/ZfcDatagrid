<?php

namespace ZfcDatagrid\Service;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcDatagrid\Datagrid;

abstract class AbstractDatagrid extends Datagrid implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return $this
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setServiceLocator($container);
        $config = $container->get('config');

        if (!isset($config['ZfcDatagrid'])) {
            throw new InvalidArgumentException('Config key "ZfcDatagrid" is missing');
        }

        /* @var $application \Zend\Mvc\Application */
        $application = $container->get('application');

        parent::setOptions($config['ZfcDatagrid']);
        parent::setMvcEvent($application->getMvcEvent());

        if ($container->has('translator') === true) {
            parent::setTranslator($container->get('translator'));
        }

        parent::setRendererService($container->get('zfcDatagrid.renderer.'.parent::getRendererName()));
        parent::init();

        return $this;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Datagrid
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Datagrid::class);
    }

    /**
     * Call initGrid on rendering.
     */
    public function render()
    {
        $this->initGrid();

        parent::render();
    }

    abstract public function initGrid();
}
