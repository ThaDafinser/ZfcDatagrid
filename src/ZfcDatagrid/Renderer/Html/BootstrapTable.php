<?php
namespace ZfcDatagrid\Renderer\Html;

use ZfcDatagrid\Renderer\AbstractRenderer;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Http\PhpEnvironment\Request as HttpRequest;

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

    /**
     *
     * @see \ZfcDatagrid\Renderer\AbstractRenderer::getSortConditions()
     *
     * @return array
     */
    public function getSortConditions (Request $request)
    {
        if(!$request instanceof HttpRequest){
            throw new \Exception('Must be an instance of HttpRequest for HTML rendering');
        }
        
        $sortConditions = array();
        
        $parameters = $this->getOptions()['parameters'];
        if ($request instanceof HttpRequest && $request->getPost($parameters['sortColumn'], $request->getQuery($parameters['sortColumn'])) != '') {
            $sortColumn = $request->getPost($parameters['sortColumn'], $request->getQuery($parameters['sortColumn']));
            $sortDirection = $request->getPost($parameters['sortDirection'], $request->getQuery($parameters['sortDirection']));
            if ($sortDirection != 'ASC' && $sortDirection != 'DESC') {
                $sortDirection = 'ASC';
            }
            
            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                if ($column->getUniqueId() == $sortColumn) {
                    $sortConditions[1] = array(
                        'sortDirection' => $sortDirection,
                        'column' => $column
                    );
                    
                    $column->setSortActive(true, $sortDirection);
                }
            }
        }
        
        return $sortConditions;
    }

    public function getFilters (Request $request)
    {
        if(!$request instanceof HttpRequest){
            throw new \Exception('Must be an instance of HttpRequest for HTML rendering');
        }
        
        $filters = array();
        
        if ($request->isPost() === true && $request->getPost('toolbarFilters') !== null) {
            foreach ($request->getPost('toolbarFilters') as $uniqueId => $value) {
                if ($value != '') {
                    foreach ($this->getColumns() as $column) {
                        /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                        if ($column->getUniqueId() == $uniqueId) {
                            $filter = new \ZfcDatagrid\Filter();
                            $filter->setFromColumn($column, $value);
                            
                            $filters[] = $filter;
                            
                            $column->setFilterActive(true, $filter->getDisplayValue());
                        }
                    }
                }
            }
        }
        
        return $filters;
    }

    public function isExport ()
    {
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
