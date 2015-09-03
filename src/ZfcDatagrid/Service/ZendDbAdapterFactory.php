<?php
namespace ZfcDatagrid\Service;

use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZendDbAdapterFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $sm
     * @return Adapter
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('config');

        return new Adapter($config['zfcDatagrid_dbAdapter']);
    }
}
