<?php
namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TableRowFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     * @return TableRow
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tableRow = new TableRow();
        if ($serviceLocator->getServiceLocator()->has('translator')) {
            /** @noinspection PhpParamsInspection */
            $tableRow->setTranslator($serviceLocator->getServiceLocator()->get('translator'));
        }

        return $tableRow;
    }
}
