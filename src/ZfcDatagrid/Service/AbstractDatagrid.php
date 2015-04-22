<?php
namespace ZfcDatagrid\Service;

use InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcDatagrid\Datagrid;

abstract class AbstractDatagrid extends Datagrid implements FactoryInterface
{
    private $isResponse = false;

    /**
     *
     * @return Datagrid
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $this->setServiceLocator($sm);

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
        parent::init();

        return $this;
    }

    /**
     * Call initGrid on rendering
     */
    public function render()
    {
        $this->initGrid();

        return parent::render();
    }

    /**
     *
     * @return void
     */
    abstract public function initGrid();
}
