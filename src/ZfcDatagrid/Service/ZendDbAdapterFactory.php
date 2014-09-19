<?php
namespace ZfcDatagrid\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;

class ZendDbAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('config');

        return new Adapter($config['zfcDatagrid_dbAdapter']);
    }
}
