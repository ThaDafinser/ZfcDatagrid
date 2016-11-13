<?php
namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TableRowFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TableRow
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $tableRow = new TableRow();
        if ($container->has('translator')) {
            $tableRow->setTranslator($container->get('translator'));
        }

        return $tableRow;
    }
}
