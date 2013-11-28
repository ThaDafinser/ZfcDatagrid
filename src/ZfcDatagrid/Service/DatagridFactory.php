<?php
namespace ZfcDatagrid\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcDatagrid\Datagrid;

class DatagridFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('config');
        $dataGrid = new Datagrid();
        $dataGrid->setOptions($config['ZfcDatagrid']);
        $dataGrid->setMvcEvent($sm->get('application')
            ->getMvcEvent());
        if ($sm->has('translator') === true) {
            $dataGrid->setTranslator($sm->get('translator'));
        }
        $dataGrid->init();
        
        return $dataGrid;
    }
}
