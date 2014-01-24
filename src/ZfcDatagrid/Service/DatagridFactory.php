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
        $grid = new Datagrid();
        $grid->setOptions($config['ZfcDatagrid']);
        $grid->setMvcEvent($sm->get('application')
            ->getMvcEvent());
        if ($sm->has('translator') === true) {
            $grid->setTranslator($sm->get('translator'));
        }
        $grid->init();
        
        return $grid;
    }
}
