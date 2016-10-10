<?php

namespace ZfcDatagrid\Renderer;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Zend\Cache;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Filter;

abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Cache\Storage\StorageInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheId;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var \ZfcDatagrid\Column\AbstractColumn[]
     */
    protected $columns = [];

    /**
     * @var \ZfcDataGrid\Column\Style\AbstractStyle[]
     */
    protected $rowStyles = [];

    /**
     * @var array
     */
    protected $sortConditions = null;

    /**
     * @var Filter[]
     */
    protected $filters = null;

    /**
     * @var int
     */
    protected $currentPageNumber = null;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var MvcEvent
     */
    protected $mvcEvent;

    /**
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $templateToolbar;

    /**
     * @var array
     */
    protected $toolbarTemplateVariables = [];

    /**
     * @var Translator
     */
    protected $translator;

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getOptionsRenderer()
    {
        $options = $this->getOptions();
        if (isset($options['renderer'][$this->getName()])) {
            return $options['renderer'][$this->getName()];
        } else {
            return [];
        }
    }

    /**
     * @param ViewModel $viewModel
     */
    public function setViewModel(ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * Set the view template.
     *
     * @param string $name
     */
    public function setTemplate($name)
    {
        $this->template = (string) $name;
    }

    /**
     * Get the view template name.
     *
     * @return string
     */
    public function getTemplate()
    {
        if (null === $this->template) {
            $this->template = $this->getTemplatePathDefault('layout');
        }

        return $this->template;
    }

    /**
     * Get the default template path (if there is no own set).
     *
     * @param string $type layout or toolbar
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getTemplatePathDefault($type = 'layout')
    {
        $optionsRenderer = $this->getOptionsRenderer();
        if (isset($optionsRenderer['templates'][$type])) {
            return $optionsRenderer['templates'][$type];
        }

        if ('layout' === $type) {
            return 'zfc-datagrid/renderer/'.$this->getName().'/'.$type;
        } elseif ('toolbar' === $type) {
            return 'zfc-datagrid/toolbar/toolbar';
        }

        throw new \Exception('Unknown type: "'.$type.'"');
    }

    /**
     * Set the toolbar view template name.
     *
     * @param string $name
     */
    public function setToolbarTemplate($name)
    {
        $this->templateToolbar = (string) $name;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getToolbarTemplate()
    {
        if (null === $this->templateToolbar) {
            $this->templateToolbar = $this->getTemplatePathDefault('toolbar');
        }

        return $this->templateToolbar;
    }

    /**
     * Set the toolbar view template variables.
     *
     * @param array $variables
     */
    public function setToolbarTemplateVariables(array $variables)
    {
        $this->toolbarTemplateVariables = $variables;
    }

    /**
     * Get the toolbar template variables.
     *
     * @return array
     */
    public function getToolbarTemplateVariables()
    {
        return $this->toolbarTemplateVariables;
    }

    /**
     * Paginator is here to retreive the totalItemCount, count pages, current page
     * NOT FOR THE ACTUAL DATA!!!!
     *
     * @param \Zend\Paginator\Paginator $paginator
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * Set the columns.
     *
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Get all columns.
     *
     * @return \ZfcDatagrid\Column\AbstractColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param \ZfcDataGrid\Column\Style\AbstractStyle[] $rowStyles
     */
    public function setRowStyles($rowStyles = [])
    {
        $this->rowStyles = $rowStyles;
    }

    /**
     * @return \ZfcDataGrid\Column\Style\AbstractStyle[]
     */
    public function getRowStyles()
    {
        return $this->rowStyles;
    }

    /**
     * Calculate the sum of the displayed column width to 100%.
     *
     * @param array $columns
     */
    protected function calculateColumnWidthPercent(array $columns)
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
            $widthSum += (($column->getWidth() / $relativeOnePercent));
            $column->setWidth(($column->getWidth() / $relativeOnePercent));
        }
    }

    /**
     * The prepared data.
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getCacheData()
    {
        return $this->getCache()->getItem($this->getCacheId());
    }

    /**
     * @throws \Exception
     *
     * @return array|false
     */
    private function getCacheSortConditions()
    {
        $cacheData = $this->getCacheData();
        if (!isset($cacheData['sortConditions'])) {
            return false;
        }

        return $cacheData['sortConditions'];
    }

    /**
     * @throws \Exception
     *
     * @return array|false
     */
    private function getCacheFilters()
    {
        $cacheData = $this->getCacheData();
        if (!isset($cacheData['filters'])) {
            return false;
        }

        return $cacheData['filters'];
    }

    /**
     * Not used ATM...
     *
     * @see \ZfcDatagrid\Renderer\RendererInterface::setMvcEvent()
     */
    public function setMvcEvent(MvcEvent $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
    }

    /**
     * Not used ATM...
     *
     * @return MvcEvent
     */
    public function getMvcEvent()
    {
        return $this->mvcEvent;
    }

    /**
     * @return \Zend\Stdlib\RequestInterface
     */
    public function getRequest()
    {
        return $this->getMvcEvent()->getRequest();
    }

    /**
     * @param Translator $translator
     *
     * @throws \InvalidArgumentException
     */
    public function setTranslator($translator)
    {
        if (!$translator instanceof Translator && !$translator instanceof \Zend\I18n\Translator\TranslatorInterface) {
            throw new \InvalidArgumentException('Translator must be an instanceof "Zend\I18n\Translator\Translator" or "Zend\I18n\Translator\TranslatorInterface"');
        }

        $this->translator = $translator;
    }

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->getTranslator() ? $this->getTranslator()->translate($string) : $string;
    }

    /**
     * Set the title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param Cache\Storage\StorageInterface $cache
     */
    public function setCache(Cache\Storage\StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param string $cacheId
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
    }

    /**
     * @return string
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * Set the sort conditions explicit (e.g.
     * from a custom form).
     *
     * @param array $sortConditions
     */
    public function setSortConditions(array $sortConditions)
    {
        foreach ($sortConditions as $sortCondition) {
            if (!is_array($sortCondition)) {
                throw new InvalidArgumentException('Sort condition have to be an array');
            }

            if (!array_key_exists('column', $sortCondition)) {
                throw new InvalidArgumentException('Sort condition missing array key column');
            }
        }

        $this->sortConditions = $sortConditions;
    }

    /**
     * @return array
     */
    public function getSortConditions()
    {
        if (is_array($this->sortConditions)) {
            return $this->sortConditions;
        }

        if ($this->isExport() === true && $this->getCacheSortConditions() !== false) {
            // Export renderer should always retrieve the sort conditions from cache!
            $this->sortConditions = $this->getCacheSortConditions();

            return $this->sortConditions;
        }

        $this->sortConditions = $this->getSortConditionsDefault();

        return $this->sortConditions;
    }

    /**
     * Get the default sort conditions defined for the columns.
     *
     * @return array
     */
    public function getSortConditionsDefault()
    {
        $sortConditions = [];
        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            if ($column->hasSortDefault() === true) {
                $sortDefaults = $column->getSortDefault();

                $sortConditions[$sortDefaults['priority']] = [
                    'column' => $column,
                    'sortDirection' => $sortDefaults['sortDirection'],
                ];

                $column->setSortActive($sortDefaults['sortDirection']);
            }
        }

        ksort($sortConditions);

        return $sortConditions;
    }

    /**
     * Set filters explicit (e.g.
     * from a custom form).
     *
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        foreach ($filters as $filter) {
            if (!$filter instanceof Filter) {
                throw new InvalidArgumentException('Filter have to be an instanceof ZfcDatagrid\Filter');
            }
        }

        $this->filters = $filters;
    }

    /**
     * @return Filter[]
     */
    public function getFilters()
    {
        if (is_array($this->filters)) {
            return $this->filters;
        }

        if ($this->isExport() === true && $this->getCacheFilters() !== false) {
            // Export renderer should always retrieve the filters from cache!
            $this->filters = $this->getCacheFilters();

            return $this->filters;
        }

        $this->filters = $this->getFiltersDefault();

        return $this->filters;
    }

    /**
     * Get the default filter conditions defined for the columns.
     *
     * @return Filter[]
     */
    public function getFiltersDefault()
    {
        $filters = [];

        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            if ($column->hasFilterDefaultValue() === true) {
                $filter = new Filter();
                $filter->setFromColumn($column, $column->getFilterDefaultValue());
                $filters[] = $filter;

                $column->setFilterActive($filter->getDisplayColumnValue());
            }
        }

        return $filters;
    }

    /**
     * Set the current page number.
     *
     * @param int $page
     */
    public function setCurrentPageNumber($page)
    {
        $this->currentPageNumber = (int) $page;
    }

    /**
     * Should be implemented for each renderer itself (just default).
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        if (null === $this->currentPageNumber) {
            $this->currentPageNumber = 1;
        }

        return (int) $this->currentPageNumber;
    }

    /**
     * Should be implemented for each renderer itself (just default).
     *
     * @return int
     */
    public function getItemsPerPage($defaultItems = 25)
    {
        if ($this->isExport() === true) {
            return (int) -1;
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
    public function prepareViewModel(Datagrid $grid)
    {
        $viewModel = $this->getViewModel();

        $viewModel->setVariable('gridId', $grid->getId());
        $viewModel->setVariable('title', $this->getTitle());
        $viewModel->setVariable('parameters', $grid->getParameters());
        $viewModel->setVariable('overwriteUrl', $grid->getUrl());

        $viewModel->setVariable('templateToolbar', $this->getToolbarTemplate());
        foreach ($this->getToolbarTemplateVariables() as $key => $value) {
            $viewModel->setVariable($key, $value);
        }
        $viewModel->setVariable('rendererName', $this->getName());

        $options = $this->getOptions();
        $generalParameterNames = $options['generalParameterNames'];
        $viewModel->setVariable('generalParameterNames', $generalParameterNames);

        $viewModel->setVariable('columns', $this->getColumns());

        $viewModel->setVariable('rowStyles', $grid->getRowStyles());

        $viewModel->setVariable('paginator', $this->getPaginator());
        $viewModel->setVariable('data', $this->getData());
        $viewModel->setVariable('filters', $this->getFilters());

        $viewModel->setVariable('rowClickAction', $grid->getRowClickAction());
        $viewModel->setVariable('massActions', $grid->getMassActions());

        $viewModel->setVariable('isUserFilterEnabled', $grid->isUserFilterEnabled());

        /*
         * renderer specific parameter names
         */
        $optionsRenderer = $this->getOptionsRenderer();
        $viewModel->setVariable('optionsRenderer', $optionsRenderer);
        if ($this->isExport() === false) {
            $parameterNames = $optionsRenderer['parameterNames'];
            $viewModel->setVariable('parameterNames', $parameterNames);

            $activeParameters = [];
            $activeParameters[$parameterNames['currentPage']] = $this->getCurrentPageNumber();
            {
                $sortColumns = [];
                $sortDirections = [];
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

    /**
     * Return the name of the renderer.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Determine if the renderer is for export.
     *
     * @return bool
     */
    abstract public function isExport();

    /**
     * Determin if the renderer is HTML
     * It can be export + html -> f.x.
     * printing for HTML.
     *
     * @return bool
     */
    abstract public function isHtml();

    /**
     * Execute all...
     *
     * @return ViewModel Response\Stream
     */
    abstract public function execute();
}
