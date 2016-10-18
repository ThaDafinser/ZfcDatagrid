<?php

namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TableRowFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TableRow
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $tableRow = new TableRow();
        if ($container->has('translator')) {
            $tableRow->setTranslator($container->get('translator'));
        }

        return $tableRow;
    }

    /**
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     *
     * @return TableRow
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), TableRow::class);
    }
}
