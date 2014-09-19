<?php
namespace ZfcDatagrid\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcDatagrid\Datagrid;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;

class DatagridFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('config');

        if (! isset($config['ZfcDatagrid'])) {
            throw new InvalidArgumentException('Config key "ZfcDatagrid" is missing');
        }

        /* @var $application \Zend\Mvc\Application */
        $application = $sm->get('application');

        $grid = new Datagrid();
        $grid->setOptions($config['ZfcDatagrid']);
        $grid->setMvcEvent($application->getMvcEvent());
        if ($sm->has('translator') === true) {
            $grid->setTranslator($sm->get('translator'));
        }
        $grid->init();

        return $grid;
    }
}
