<?php
namespace ZfcDatagrid\Renderer\JqGrid;

use ZfcDatagrid\Renderer\AbstractRenderer;
use ZfcDatagrid\Column;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\View\Model\JsonModel;

class Renderer extends AbstractRenderer
{

    public function getName()
    {
        return 'jqGrid';
    }

    public function isHtml()
    {
        return true;
    }

    public function isExport()
    {
        return false;
    }

    /**
     *
     * @see \ZfcDatagrid\Renderer\AbstractRenderer::getSortConditions()
     *
     * @return array
     */
    public function getSortConditions()
    {
        if (is_array($this->sortConditions)) {
            // set from cache! (for export)
            return $this->sortConditions;
        }
        
        $request = $this->getRequest();
        if (! $request instanceof HttpRequest) {
            throw new \Exception('Must be an instance of HttpRequest for HTML rendering');
        }
        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];
        
        $sortConditions = array();
        
        $sortColumns = $request->getPost($parameterNames['sortColumns']);
        $sortDirections = $request->getPost($parameterNames['sortDirections']);
        if ($sortColumns != '') {
            $sortColumns = explode(',', $sortColumns);
            $sortDirections = explode(',', $sortDirections);
            
            if (count($sortColumns) != count($sortDirections)) {
                throw new \Exception('Count missmatch order columns/direction');
            }
            
            foreach ($sortColumns as $key => $sortColumn) {
                $sortDirection = strtoupper($sortDirections[$key]);
                
                if ($sortDirection != 'ASC' && $sortDirection != 'DESC') {
                    $sortDirection = 'ASC';
                }
                
                foreach ($this->getColumns() as $column) {
                    /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                    if ($column->getUniqueId() == $sortColumn) {
                        $sortConditions[] = array(
                            'sortDirection' => $sortDirection,
                            'column' => $column
                        );
                        
                        $column->setSortActive($sortDirection);
                    }
                }
            }
        }
        
        if (count($sortConditions) > 0) {
            $this->sortConditions = $sortConditions;
        } else {
            // No user sorting -> get default sorting
            $this->sortConditions = $this->getSortConditionsDefault();
        }
        
        return $this->sortConditions;
    }

    public function getFilters()
    {
        if (is_array($this->filters)) {
            // set from cache! (for export)
            return $this->filters;
        }
        
        $request = $this->getRequest();
        if (! $request instanceof HttpRequest) {
            throw new \Exception('Must be an instance of HttpRequest for HTML rendering');
        }
        
        $filters = array();
        
        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];
        
        $isSearch = $request->getPost($parameterNames['isSearch']);
        if ($isSearch == 'true') {
            // User filtering
            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                if ($request->getPost($column->getUniqueId()) != '') {
                    $value = $request->getPost($column->getUniqueId());
                    
                    $filter = new \ZfcDatagrid\Filter();
                    $filter->setFromColumn($column, $value);
                    
                    $filters[] = $filter;
                    
                    $column->setFilterActive($filter->getDisplayColumnValue());
                }
            }
        }
        
        if (count($filters) === 0) {
            // No user sorting -> get default sorting
            $filters = $this->getFiltersDefault();
        }
        
        $this->filters = $filters;
        
        return $this->filters;
    }
    
    /**
     * Get table classes
     *
     * @return array
     */
    public function getTableClasses ()
    {
        return $this->tableClasses;
    }

    public function getCurrentPageNumber()
    {
        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];
        
        $request = $this->getRequest();
        if ($request instanceof HttpRequest) {
            $currentPage = $request->getPost($parameterNames['currentPage']);
            if ($currentPage != '') {
                $this->currentPageNumber = (int) $currentPage;
            }
        }
        
        return (int) $this->currentPageNumber;
    }

    public function execute()
    {
        $request = $this->getRequest();
        if ($request instanceof HttpRequest && $request->isXmlHttpRequest() === true && $request->isPost() === true && $request->getPost('nd') != '') {
            // AJAX Request...load only data...
            $viewModel = new JsonModel();
            $viewModel->setVariable('data', $this->getDataJqGrid());
        } else {
            $viewModel = $this->getViewModel();
            $viewModel->setTemplate($this->getTemplate());
            $viewModel->setVariable('data', $this->getDataJqGrid());
            
            $columnsRowClickDisabled = array();
            $columns = $viewModel->getVariable('columns');
            foreach ($columns as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                
                if ($column->isRowClickEnabled() !== true) {
                    $columnsRowClickDisabled[] = $column->getUniqueId();
                }
            }
            
            $viewModel->setVariable('columnsRowClickDisabled', $columnsRowClickDisabled);
        }
        
        return $viewModel;
    }

    public function getData()
    {
        $data = parent::getData();
        
        foreach ($data as &$row) {
            foreach ($this->getColumns() as $column) {
                if ($column instanceof Column\Select) {
                    // $row[$column->getUniqueId()] = nl2br($row[$column->getUniqueId()], true);
                } elseif ($column instanceof Column\Action) {
                    /* @var $column \ZfcDatagrid\Column\Action */
                    
                    $actions = array();
                    foreach ($column->getActions() as $action) {
                        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
                        
                        if ($action->isDisplayed($row) === true) {
                            $actions[] = $action->toHtml($row);
                        }
                    }
                    
                    $row[$column->getUniqueId()] = implode(' ', $actions);
                } elseif ($column instanceof Column\Icon) {
                    $row[$column->getUniqueId()] = $column->getIconClass();
                }
            }
        }
        
        return $data;
    }

    private function getDataJqGrid()
    {
        $data = $this->getData();
        
        return array(
            'rows' => $this->getData(),
            'page' => $this->getPaginator()->getCurrentPageNumber(),
            'total' => $this->getPaginator()->count(),
            'records' => $this->getPaginator()->getTotalItemCount()
        );
    }
}
