<?php
namespace ZfcDatagrid\Renderer;

use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use ZfcDatagrid\Datagrid;
use Zend\I18n\Translator\Translator;


abstract class AbstractRenderer implements RendererInterface
{

    protected $options = array();
    
    protected $title;
    
    /**
     *
     * @var Paginator
     */
    protected $paginator;

    protected $columns = array();
    
    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var MvcEvent
     */
    protected $mvcEvent;

    /**
     *
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * 
     * @var Translator
     */
    protected $translator;
    

    public function setOptions (array $config)
    {
        $this->options = $config;
    }
    
    /**
     *
     * @return array
     */
    public function getOptions ()
    {
        return $this->options;
    }
    
    public function setPaginator (Paginator $paginator)
    {
        $this->paginator = $paginator;
    }
    
    /**
     * 
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator(){
        return $this->paginator;
    }

    public function setColumns (array $columns)
    {
        $this->columns = $columns;
    }
    
    public function getColumns(){
        return $this->columns;
    }
    
    /**
     * The prepared data
     *
     * @param array $data            
     */
    public function setData (array $data)
    {
        $this->data = $data;
    }

    public function getData(){
        return $this->data;
    }
    
    /**
     * Not used ATM...
     * @deprecated
     * 
     * @see \ZfcDatagrid\Renderer\RendererInterface::setMvcEvent()
     */
    public function setMvcEvent (MvcEvent $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
    }
    
    /**
     * Not used ATM...
     * @deprecated
     * 
     * @return MvcEvent
     */
    public function getMvcEvent(){
        return $this->mvcEvent;
    }

    public function setViewModel (ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
    }
    
    /**
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function getViewModel(){
        return $this->viewModel;
    }
    
    public function setTranslator(Translator $translator){
        $this->translator = $translator;
    }
    
    public function getTranslator(){
        return $this->translator;
    }
    
    public function setTitle($title){
        $this->title = $title;
    }
    
    public function getTitle(){
        return $this->title;
    }

    /**
     * VERY UGLY DEPENDECY...
     *
     * @todo Refactor :-)
     *      
     * @see \ZfcDatagrid\Renderer\RendererInterface::prepareViewModel()
     */
    public function prepareViewModel (Datagrid $grid)
    {
        $parameterNames = $this->getOptions()['parameters'];
        $viewModel = $this->viewModel;
        $viewModel->setVariable('title', $this->getTitle());
        $viewModel->setVariable('parameterNames', $parameterNames);
        
        $activeParameters = array();
        $activeParameters[$parameterNames['currentPage']] = $grid->getCurrentPage();
        if ($grid->isUserSortActive() === true) {
            $sortCondition = $grid->getSortConditions();
            $sortCondition = array_pop($sortCondition);
            
            $activeParameters[$parameterNames['sortColumn']] = $sortCondition['column']->getUniqueId();
            $activeParameters[$parameterNames['sortDirection']] = $sortCondition['sortDirection'];
        }
        $viewModel->setVariable('activeParameters', $activeParameters);
        
//         $viewModel->setVariable('gridId', $grid->getGridId());
        $viewModel->setVariable('columns', $this->getColumns());
        $viewModel->setVariable('paginator', $this->getPaginator());
        $viewModel->setVariable('data', $this->getData());
    }
}
