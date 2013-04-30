<?php
namespace ZfcDatagrid\Renderer;

use ZfcDatagrid\Datagrid;
use Zend\Paginator\Paginator;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;

interface RendererInterface
{

    /**
     * Paginator is here to retreive the totalItemCount, count pages, current page, .
     * ..
     *
     * NOT FOR THE ACTUAL DATA!!!!
     *
     * @param \Zend\Paginator\Paginator $paginator            
     */
    public function setPaginator (Paginator $paginator);

    /**
     * The prepared data
     *
     * @param array $data            
     */
    public function setData (array $data);

    /**
     *
     * @param MvcEvent $mvcEvent            
     */
    public function setMvcEvent (MvcEvent $mvcEvent);

    /**
     * Set the viewModel
     * 
     * @param ViewModel $viewModel
     */
    public function setViewModel (ViewModel $viewModel);
    
    /**
     * Populates the view with variables
     */
    public function prepareViewModel(Datagrid $grid);
    
    /**
     * @return boolean
     */
    public function isExport();
    
    /**
     * Execute all...
     * 
     * @return ViewModel|Response\Stream
     */
    public function execute();
}
