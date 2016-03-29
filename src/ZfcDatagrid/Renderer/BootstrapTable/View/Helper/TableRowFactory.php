<?php


namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TableRowFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return TableRow
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tableRow = new TableRow();
        if($serviceLocator->has('translator')){
            /** @noinspection PhpParamsInspection */
            $tableRow->setTranslator($serviceLocator->get('translator'));
        }

        return $tableRow;
    }

}