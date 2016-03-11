<?php
namespace ZfcDatagrid\Service;

use InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcDatagrid\Datagrid;

abstract class AbstractDatagrid extends Datagrid implements FactoryInterface
{
    /**
     *
     * @return Datagrid
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('config');

        if (! isset($config['ZfcDatagrid'])) {
            throw new InvalidArgumentException('Config key "ZfcDatagrid" is missing');
        }

        /* @var $application \Zend\Mvc\Application */
        $application = $sm->get('application');

        parent::setOptions($config['ZfcDatagrid']);
        parent::setMvcEvent($application->getMvcEvent());
        if ($sm->has('translator') === true) {
            parent::setTranslator($sm->get('translator'));
        }
        /** @noinspection PhpParamsInspection */
        parent::setRendererService($sm->get('zfcDatagrid.renderer.' . parent::getRendererName()));
        parent::init();

        return $this;
    }

    /**
     * Call initGrid on rendering
     */
    public function render()
    {
        $this->initGrid();

        parent::render();
    }

    /**
     *
     * @return void
     */
    abstract public function initGrid();
}
