<?php
namespace ZfcDatagrid\Renderer\JqGrid\View\Helper;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ColumnsFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     * @return Columns
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tableRow = new Columns();
        if ($serviceLocator->getServiceLocator()->has('translator')) {
            /** @noinspection PhpParamsInspection */
            $tableRow->setTranslator($serviceLocator->getServiceLocator()->get('translator'));
        }

        return $tableRow;
    }
}
