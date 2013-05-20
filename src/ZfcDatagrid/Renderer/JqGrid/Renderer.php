<?php
namespace ZfcDatagrid\Renderer\JqGrid;

use ZfcDatagrid\Renderer\AbstractRenderer;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\View\Model\JsonModel;

class Renderer extends AbstractRenderer
{

    protected $template = 'zfc-datagrid/renderer/jqGrid/table';

    public function setTemplate ($name = 'zfc-datagrid/renderer/jqGrid/table')
    {
        $this->template = (string) $name;
    }

    public function getTemplate ()
    {
        return $this->template;
    }

    private function getRendererOptions ()
    {
        return $this->getOptions()['renderer']['jqGrid'];
    }

    /**
     *
     * @see \ZfcDatagrid\Renderer\AbstractRenderer::getSortConditions()
     *
     * @return array
     */
    public function getSortConditions ()
    {
        if (is_array($this->sortConditions)) {
            // set from cache! (for export)
            return $this->sortConditions;
        }
        
        $request = $this->getRequest();
        if (! $request instanceof HttpRequest) {
            throw new \Exception('Must be an instance of HttpRequest for HTML rendering');
        }
        $rendererOptions = $this->getRendererOptions();
        $parameterNames = $rendererOptions['parameterNames'];
        
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
                        
                        $column->setSortActive(true, $sortDirection);
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

    public function getFilters ()
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
        
        $rendererOptions = $this->getRendererOptions();
        $parameterNames = $rendererOptions['parameterNames'];
        
        $isSearch = $request->getPost($parameterNames['isSearch']);
        if($isSearch == 'true'){
            //User filtering
            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                if ($request->getPost($column->getUniqueId()) != '') {
                    $value = $request->getPost($column->getUniqueId());
                    
                    $filter = new \ZfcDatagrid\Filter();
                    $filter->setFromColumn($column, $value);
                    
                    $filters[] = $filter;
                    
                    $column->setFilterActive(true, $filter->getDisplayValue());
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

    public function getCurrentPageNumber ()
    {
        $rendererOptions = $this->getRendererOptions();
        $parameterNames = $rendererOptions['parameterNames'];
        
        $request = $this->getRequest();
        if ($request instanceof HttpRequest) {
            $currentPage = $request->getPost($parameterNames['currentPage']);
            if ($currentPage != '') {
                $this->currentPageNumber = (int) $currentPage;
            }
        }
        
        return (int) $this->currentPageNumber;
    }

    public function isHtml ()
    {
        return true;
    }

    public function isExport ()
    {
        return false;
    }

    public function execute ()
    {
        $request = $this->getRequest();
        if ($request instanceof HttpRequest && $request->isXmlHttpRequest() === true) {
            // AJAX Request...load only data...
            $viewModel = new JsonModel();
            $viewModel->setVariable('data', $this->getDataJqGrid());
        } else {
            $viewModel = $this->getViewModel();
            $viewModel->setTemplate($this->getTemplate());
            
            $this->setRendererOptions($this->getRendererOptions());
            
            $viewModel->setVariable('data', $this->getDataJqGrid());
        }
        
        return $viewModel;
    }

    private function getDataJqGrid ()
    {
        return array(
            'rows' => $this->getData(),
            'page' => $this->getPaginator()->getCurrentPageNumber(),
            'total' => $this->getPaginator()->count(),
            'records' => $this->getPaginator()->getTotalItemCount()
        );
    }
}
