<?php
namespace ZfcDatagrid\Renderer\Html;

use ZfcDatagrid\Renderer\AbstractRenderer;

class BootstrapTable extends AbstractRenderer
{

    protected $template = 'zfc-datagrid/renderer/html/bootstrap-table';

    public function setTemplate ($name = 'zfc-datagrid/renderer/html/bootstrap-table')
    {
        $this->template = (string) $name;
    }

    public function getTemplate ()
    {
        return $this->template;
    }
    
    public function isExport(){
        return false;
    }

    public function execute ()
    {
        $viewModel = $this->getViewModel();
        
        // $viewModel->setVariable('title', $this->getTitle());
        $viewModel->setTemplate($this->getTemplate());
        
        return $viewModel;
    }
}
