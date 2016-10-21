<?php

namespace ZfcDatagrid\Renderer\PrintHtml;

use Zend\View\Model\ViewModel;
use ZfcDatagrid\Renderer\AbstractRenderer;

class Renderer extends AbstractRenderer
{
    public function getName()
    {
        return 'printHtml';
    }

    public function isExport()
    {
        return true;
    }

    public function isHtml()
    {
        return true;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function execute()
    {
        $layout = $this->getViewModel();
        $layout->setTemplate($this->getTemplate());
        $layout->setTerminal(true);

        $table = new ViewModel();
        $table->setTemplate('zfc-datagrid/renderer/printHtml/table');
        $table->setVariables($layout->getVariables());

        $layout->addChild($table, 'table');

        return $layout;
    }
}
