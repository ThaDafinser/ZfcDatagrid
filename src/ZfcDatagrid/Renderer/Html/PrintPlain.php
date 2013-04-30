<?php
namespace ZfcDatagrid\Renderer\Html;

use ZfcDatagrid\Renderer\AbstractRenderer;
use Zend\Http\Response;
use Zend\Http;

class PrintPlain extends AbstractRenderer
{
    protected $template = 'zfc-datagrid/renderer/html/print-plain';
    
    public function setTemplate ($name = 'zfc-datagrid/renderer/html/print-plain')
    {
        $this->template = (string) $name;
    }
    
    public function getTemplate ()
    {
        return $this->template;
    }

    public function isExport(){
        return true;
    }
    
    /**
     * @return Response\Stream
     */
    public function execute ()
    {
        $viewModel = $this->getViewModel();
        
        // $viewModel->setVariable('title', $this->getTitle());
        $viewModel->setTemplate($this->getTemplate());
        
        return $viewModel;
        
        print_r($this->getData());
        exit();
    }
}
