<?php
namespace ZfcDatagrid\Renderer;

use Zend\Stdlib\ResponseInterface;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use ZfcDatagrid\Datagrid;

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
     * @param ResponseInterface $response            
     */
    public function setResponse (ResponseInterface $response);

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
     * Execute all...
     */
    public function execute();
}
