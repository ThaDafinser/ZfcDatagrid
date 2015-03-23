<?php
namespace ZfcDatagrid\Renderer\BootstrapTable;

use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Renderer\AbstractRenderer;
use Zend\Http\PhpEnvironment\Request as HttpRequest;

class Renderer extends AbstractRenderer
{
    public function getName()
    {
        return 'bootstrapTable';
    }

    public function isExport()
    {
        return false;
    }

    public function isHtml()
    {
        return true;
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
            // set from cache! (for export)
            return $this->sortConditions;
        }

        $request = $this->getRequest();

        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];

        $sortConditions = array();
        $sortColumns = $request->getPost($parameterNames['sortColumns'], $request->getQuery($parameterNames['sortColumns']));
        $sortDirections = $request->getPost($parameterNames['sortDirections'], $request->getQuery($parameterNames['sortDirections']));
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
                            'column' => $column,
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

    /**
     *
     * @todo Make parameter config
     *
     * @see \ZfcDatagrid\Renderer\AbstractRenderer::getFilters()
     */
    public function getFilters()
    {
        if (is_array($this->filters)) {
            return $this->filters;
        }

        $request = $this->getRequest();

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

                            $column->setFilterActive($filter->getDisplayColumnValue());
                        }
                    }
                }
            }
        }

        if (count($filters) > 0) {
            $this->filters = $filters;
        } else {
            // No user sorting -> get default sorting
            $this->filters = $this->getFiltersDefault();
        }

        return $this->filters;
    }

    public function getCurrentPageNumber()
    {
        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];

        if ($this->getRequest() instanceof HttpRequest) {
            $this->currentPageNumber = (int) $this->getRequest()->getPost($parameterNames['currentPage'], $this->getRequest()
                ->getQuery($parameterNames['currentPage'], 1));
        }

        return (int) $this->currentPageNumber;
    }

    public function prepareViewModel(Datagrid $grid)
    {
        parent::prepareViewModel($grid);

        $options = $this->getOptionsRenderer();

        $viewModel = $this->getViewModel();

        // Check if the datarange picker is enabled
        if (isset($options['daterange']['enabled']) && $options['daterange']['enabled'] === true) {
            $dateRangeParameters = $options['daterange']['options'];

            $viewModel->setVariable('daterangeEnabled', true);
            $viewModel->setVariable('daterangeParameters', $dateRangeParameters);
        } else {
            $viewModel->setVariable('daterangeEnabled', false);
        }
    }

    public function execute()
    {
        $viewModel = $this->getViewModel();
        $viewModel->setTemplate($this->getTemplate());

        return $viewModel;
    }
}
