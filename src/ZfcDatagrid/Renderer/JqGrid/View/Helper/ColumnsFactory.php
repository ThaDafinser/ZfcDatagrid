<?php


namespace ZfcDatagrid\Renderer\JqGrid\View\Helper;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ColumnsFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Columns
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tableRow = new Columns();
        if($serviceLocator->has('translator')){
            /** @noinspection PhpParamsInspection */
            $tableRow->setTranslator($serviceLocator->get('translator'));
        }

        return $tableRow;
    }

}