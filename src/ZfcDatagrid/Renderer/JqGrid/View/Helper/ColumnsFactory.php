<?php
namespace ZfcDatagrid\Renderer\JqGrid\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ColumnsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Columns
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $tableRow = new Columns();
        if ($container->has('translator')) {
            $tableRow->setTranslator($container->get('translator'));
        }

        return $tableRow;
    }
}
