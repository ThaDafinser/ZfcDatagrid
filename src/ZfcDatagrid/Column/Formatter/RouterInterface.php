<?php
namespace ZfcDatagrid\Column\Formatter;

use Zend\Router\RouteStackInterface;

/**
 * Interface RouterInterface
 * @package ZfcDatagrid\Column\Formatter
 */
interface RouterInterface
{
    /**
     * @param \Zend\Router\RouteStackInterface $router
     *
     * @return void
     */
    public function setRouter(RouteStackInterface $router);

    /**
     * @return \Zend\Router\RouteStackInterface
     */
    public function getRouter();
}
