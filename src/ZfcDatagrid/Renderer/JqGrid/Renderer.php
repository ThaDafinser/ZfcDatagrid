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
     * @return HttpRequest
     */
    public function getRequest()
    {
        $request = parent::getRequest();
        if (! $request instanceof HttpRequest) {
            throw new \Exception('Request must be an instance of Zend\Http\PhpEnvironment\Request for HTML rendering');
        }

        return $request;
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
            return $this->sortConditions;
        }
        $request = $this->getRequest();

        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];

        $sortConditions   = array();
        $groupSortColumns = array();

        $sortColumns = $request->getPost($parameterNames['sortColumns']);
        $sortDirections = $request->getPost($parameterNames['sortDirections']);

        // Handle user sorting
        if ($sortColumns != '') {
            $sortColumns = explode(',', $sortColumns);
            $sortDirections = explode(',', $sortDirections);

            foreach ($sortColumns as $key => $sortColumn) {
           	// Sometimes jqGrid creates empty strings inside sortByColumns when using groupingView
            	if ($sortColumn == ' ') continue;

            	$sortColumn = trim($sortColumn);

            	if (strpos($sortColumn, 'asc') !== false || strpos($sortColumn, 'desc') !== false) {
            	    list($groupSortColumn, $groupSortDirection) = explode(" ", $sortColumn);

            	    $groupSortColumns[$groupSortColumn] = strtoupper($groupSortDirection);
            	} else {
            	    // Find sortDirection for column by next `sortDirections` value
	            $sortDirection = current($sortDirections);

	            // Set default direction
	            if ($sortDirection != 'asc' && $sortDirection != 'desc') {
	                $sortDirection = 'asc';
	            }
	            $groupSortColumns[$sortColumn] = strtoupper($sortDirection);

	            next($sortDirections);
            	}
            }

            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            	if (key_exists($column->getUniqueId(), $groupSortColumns)) {
            	    $sortDirection = $groupSortColumns[$column->getUniqueId()];

                    $sortConditions[] = array(
                        'sortDirection' => $sortDirection,
                        'column'        => $column
                    );
                    $column->setSortActive($sortDirection);
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

        $filters = array();

        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];

        $request = $this->getRequest();
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
        if ($request->isXmlHttpRequest() === true && $request->isPost() === true && $request->getPost('nd') != '') {
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
