<?php
namespace ZfcDatagrid\Renderer;

use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Column;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\I18n\Translator\Translator;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Console\Request as ConsoleRequest;

abstract class AbstractRenderer implements RendererInterface
{
    
    // const TREE_DEFAULT_SIGN_CLOSED = '>';
    // const TREE_DEFAULT_SIGN_OPENED = '';
    
    // protected $treeSignClosed = self::TREE_DEFAULT_SIGN_CLOSED;
    // protected $treeSignOpened = self::TREE_DEFAULT_SIGN_OPENED;
    protected $options = array();

    protected $title;

    /**
     *
     * @var Paginator
     */
    protected $paginator;

    protected $columns = array();

    protected $sortConditions = null;

    protected $filters = null;

    protected $currentPageNumber = 1;

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
     * @var array
     */
    protected $cacheData;

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

    /**
     * Paginator is here to retreive the totalItemCount, count pages, current page, .
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     * NOT FOR THE ACTUAL DATA!!!!
     *
     * @param \Zend\Paginator\Paginator $paginator            
     */
    public function setPaginator (Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     *
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator ()
    {
        return $this->paginator;
    }

    public function setColumns (array $columns)
    {
        $this->columns = $columns;
    }

    public function getColumns ()
    {
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

    public function getData ()
    {
        return $this->data;
    }

    public function setCacheData (array $cacheData = null)
    {
        $this->cacheData = $cacheData;
    }

    private function getCacheSortConditions ()
    {
        if (! isset($this->cacheData['sortConditions'])) {
            throw new \Exception('Sort conditions from cache are missing!');
        }
        return $this->cacheData['sortConditions'];
    }

    private function getCacheFilters ()
    {
        if (! isset($this->cacheData['filters'])) {
            throw new \Exception('Filters from cache are missing!');
        }
        return $this->cacheData['filters'];
    }

    /**
     * Not used ATM...
     *
     * @see \ZfcDatagrid\Renderer\RendererInterface::setMvcEvent()
     */
    public function setMvcEvent (MvcEvent $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
    }

    /**
     * Not used ATM...
     *
     * @return MvcEvent
     */
    public function getMvcEvent ()
    {
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
    public function getViewModel ()
    {
        return $this->viewModel;
    }

    public function setTranslator (Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator ()
    {
        return $this->translator;
    }

    public function setTitle ($title)
    {
        $this->title = $title;
    }

    public function getTitle ()
    {
        return $this->title;
    }

    /**
     *
     * @return \Zend\Stdlib\RequestInterface
     */
    public function getRequest ()
    {
        return $this->getMvcEvent()->getRequest();
    }

    /**
     *
     * @return array
     */
    public function getSortConditions ()
    {
        if (is_array($this->sortConditions)) {
            // set from cache! (for export)
            return $this->sortConditions;
        } elseif ($this->isExport() === true) {
            // Export renderer should always retrieve the sort conditions from cache!
            $this->sortConditions = $this->getCacheSortConditions();
            
            return $this->sortConditions;
        }
        
        $this->sortConditions = $this->getSortConditionsDefault();
        
        return $this->sortConditions;
    }

    /**
     * Get the default sort conditions defined for the columns
     *
     * @return array
     */
    public function getSortConditionsDefault ()
    {
        $sortConditions = array();
        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            if ($column->hasSortDefault() === true) {
                $sortDefaults = $column->getSortDefault();
                
                $sortConditions[$sortDefaults['priority']] = array(
                    'sortDirection' => $sortDefaults['sortDirection'],
                    'column' => $column
                );
                
                $column->setSortActive(true, $sortDefaults['sortDirection']);
            }
        }
        
        ksort($sortConditions);
        
        return $sortConditions;
    }

    /**
     *
     * @return array
     */
    public function getFilters ()
    {
        if (is_array($this->filters)) {
            // set from cache! (for export)
            return $this->filters;
        } elseif ($this->isExport() === true) {
            // Export renderer should always retrieve the filters from cache!
            $this->filters = $this->getCacheFilters();
            
            return $this->filters;
        }
        
        $this->filters = $this->getFiltersDefault();
        
        return $this->filters;
    }

    /**
     * Get the default filter conditions defined for the columns
     *
     * @return array
     */
    public function getFiltersDefault ()
    {
        $filters = array();
        if ($this->getRequest() instanceof ConsoleRequest || ($this->getRequest() instanceof HttpRequest && ! $this->getRequest()->isPost())) {
            
            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                if ($column->hasFilterDefaultValue() === true) {
                    
                    $filter = new \ZfcDatagrid\Filter();
                    $filter->setFromColumn($column, $column->getFilterDefaultValue());
                    $filters[] = $filter;
                    
                    $column->setFilterActive(true, $filter->getDisplayValue());
                }
            }
        }
        
        return $filters;
    }

    /**
     * Should be implemented for each renderer itself (just default)
     *
     * @return integer
     */
    public function getCurrentPageNumber ()
    {
        return (int) $this->currentPageNumber;
    }

    /**
     * Should be implemented for each renderer itself (just default)
     *
     * @return integer
     */
    public function getItemsPerPage ($defaultItems = 25)
    {
        if ($this->isExport() === true) {
            return (int) - 1;
        }
        
        return $defaultItems;
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
        $viewModel = $this->getViewModel();
        
        $viewModel->setVariable('gridId', $grid->getGridId());
        
        $viewModel->setVariable('title', $this->getTitle());
        
        $generalParameterNames = $this->getOptions()['generalParameterNames'];
        $viewModel->setVariable('generalParameterNames', $generalParameterNames);
        
        $viewModel->setVariable('columns', $this->getColumns());
        $viewModel->setVariable('paginator', $this->getPaginator());
        $viewModel->setVariable('data', $this->getData());
        $viewModel->setVariable('filters', $this->getFilters());
        
        if ($grid->hasRowClickAction() === true) {
            $viewModel->setVariable('rowClickLink', $grid->getRowClickAction()
                ->getLink());
        } else {
            $viewModel->setVariable('rowClickLink', '#');
        }
        
        $viewModel->setVariable('isUserFilterEnabled', $grid->isUserFilterEnabled());
    }

    protected function setRendererOptions ($options)
    {
        $parameterNames = $options['parameterNames'];
        
        $viewModel = $this->getViewModel();
        $viewModel->setVariable('rendererOptions', $options);
        $viewModel->setVariable('parameterNames', $parameterNames);
        
        $activeParameters = array();
        $activeParameters[$parameterNames['currentPage']] = $this->getCurrentPageNumber();
        {
            $sortColumns = array();
            $sortDirections = array();
            foreach ($this->getSortConditions() as $sortCondition) {
                $sortColumns[] = $sortCondition['column']->getUniqueId();
                $sortDirections[] = $sortCondition['sortDirection'];
            }
            
            $activeParameters[$parameterNames['sortColumns']] = implode(',', $sortColumns);
            $activeParameters[$parameterNames['sortDirections']] = implode(',', $sortDirections);
        }
        $viewModel->setVariable('activeParameters', $activeParameters);
    }
}
