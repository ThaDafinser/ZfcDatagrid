<?php
namespace ZfcDatagrid\Service;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcDatagrid\Datagrid;

class DatagridFactory implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface $sm
     * @return Datagrid
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('config');

        if (!isset($config['ZfcDatagrid'])) {
            throw new InvalidArgumentException('Config key "ZfcDatagrid" is missing');
        }

        /* @var $application \Zend\Mvc\Application */
        $application = $sm->get('application');

        $grid = new Datagrid();
        $grid->setServiceLocator($sm);
        $grid->setOptions($config['ZfcDatagrid']);
        $grid->setMvcEvent($application->getMvcEvent());
        if ($sm->has('translator') === true) {
            $grid->setTranslator($sm->get('translator'));
        }
        /** @noinspection PhpParamsInspection */
        $grid->setRendererService($sm->get('zfcDatagrid.renderer.' . $grid->getRendererName()));
        $grid->init();

        return $grid;
    }
}
