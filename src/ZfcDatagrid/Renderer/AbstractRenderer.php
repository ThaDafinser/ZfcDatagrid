<?php
namespace ZfcDatagrid\Renderer;

use ZfcDatagrid\Datagrid;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\I18n\Translator\Translator;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Console\Request as ConsoleRequest;

abstract class AbstractRenderer implements RendererInterface
{

    protected $options = array();

    protected $title;

    /**
     *
     * @var string
     */
    protected $cacheId;

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

    protected $template;

    protected $templateToolbar;

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
     *
     * @return array
     */
    public function getRendererOptions ()
    {
        $options = $this->getOptions();
        if (isset($options['renderer'][$this->getName()])) {
            return $options['renderer'][$this->getName()];
        } else {
            return array();
        }
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

    public function setTemplate ($name)
    {
        $this->template = (string) $name;
    }

    public function getTemplate ()
    {
        if ($this->template === null) {
            $this->template = $this->getTemplatePathDefault('layout');
        }
        
        return $this->template;
    }

    /**
     * Get the default template path (if there is no own set)
     *
     * @param string $type
     *            layout or toolbar
     * @return string
     */
    public function getTemplatePathDefault ($type = 'layout')
    {
        $options = $this->getOptions();
        if (isset($options['renderer'][$this->getName()]['templates'][$type])) {
            return $options['renderer'][$this->getName()]['templates'][$type];
        }
        
        if ($type === 'layout') {
            return 'zfc-datagrid/renderer/' . $this->getName() . '/' . $type;
        } elseif ($type === 'toolbar') {
            return 'zfc-datagrid/toolbar/toolbar';
        }
        
        throw new \Exception('not defined: "' . $type . '"');
    }

    public function setToolbarTemplate ($name)
    {
        $this->templateToolbar = (string) $name;
    }

    public function getToolbarTemplate ()
    {
        if ($this->templateToolbar === null) {
            $this->templateToolbar = $this->getTemplatePathDefault('toolbar');
        }
        
        return $this->templateToolbar;
    }

    /**
     * Paginator is here to retreive the totalItemCount, count pages, current page
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

    /**
     * Set the columns
     *
     * @param array $columns            
     */
    public function setColumns (array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Get all columns
     *
     * @return array
     */
    public function getColumns ()
    {
        return $this->columns;
    }

    /**
     * Calculate the sum of the displayed column width to 100%
     *
     * @param array $columns            
     */
    protected function calculateColumnWidthPercent (array $columns)
    {
        $widthAllColumn = 0;
        foreach ($columns as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $widthAllColumn += $column->getWidth();
        }
        
        $widthSum = 0;
        // How much 1 percent columnd width is really "one" percent...
        $relativeOnePercent = $widthAllColumn / 100;
        
        foreach ($columns as $column) {
            $widthSum += (round($column->getWidth() / $relativeOnePercent));
            $column->setWidth(round($column->getWidth() / $relativeOnePercent));
        }
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

    public function setTranslator (Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return \Zend\I18n\Translator\Translator
     */
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

    public function setCacheId ($cacheId)
    {
        $this->cacheId = $cacheId;
    }

    public function getCacheId ()
    {
        return $this->cacheId;
    }

    /**
     * Get a valid filename to save
     * (WITHOUT the extension!)
     *
     * @return string
     */
    public function getFilename ()
    {
        $title = $this->getTitle();
        
        $filenameParts = array();
        
        $filenameParts[] = date('Y-m-d');
        
        if ($this->getTitle() != '') {
            $title = $this->getTitle();
            $title = str_replace(' ', '_', $title);
            $filenameParts[] = preg_replace("/[^a-z0-9_-]+/i", "", $title);
        }
        
        return implode('_', $filenameParts);
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
                
                $column->setSortActive($sortDefaults['sortDirection']);
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
                    
                    $column->setFilterActive($filter->getDisplayColumnValue());
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
        
        $viewModel->setVariable('gridId', $grid->getId());
        $viewModel->setVariable('title', $this->getTitle());
        $viewModel->setVariable('parameters', $grid->getParameters());
        $viewModel->setVariable('overwriteUrl', $grid->getUrl());
        
        $viewModel->setVariable('templateToolbar', $this->getToolbarTemplate());
        
        $options = $this->getOptions();
        $generalParameterNames = $options['generalParameterNames'];
        $viewModel->setVariable('generalParameterNames', $generalParameterNames);
        
        $viewModel->setVariable('columns', $this->getColumns());
        $columnsHidden = array();
        foreach ($this->getColumns() as $column) {
            if ($column->isHidden()) {
                $columnsHidden[] = $column->getUniqueId();
            }
        }
        $viewModel->setVariable('columnsHidden', $columnsHidden);
        
        $viewModel->setVariable('paginator', $this->getPaginator());
        $viewModel->setVariable('data', $this->getData());
        $viewModel->setVariable('filters', $this->getFilters());
        
        if ($grid->hasRowClickAction() === true) {
            $viewModel->setVariable('rowClickAction', $grid->getRowClickAction());
        }
        
        $viewModel->setVariable('isUserFilterEnabled', $grid->isUserFilterEnabled());
        
        /**
         * renderer specific parameter names
         */
        $options = $this->getRendererOptions();
        $viewModel->setVariable('rendererOptions', $options);
        if ($this->isExport() === false) {
            $parameterNames = $options['parameterNames'];
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
        
        $viewModel->setVariable('exportRenderers', $grid->getExportRenderers());
    }
}
