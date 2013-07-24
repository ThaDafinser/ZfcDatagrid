<?php
namespace ZfcDatagrid\Renderer\Text;

use ZfcDatagrid\Renderer\AbstractRenderer;
use ZfcDataGrid\Column\Type;
use Zend\Text\Table\Table as TextTable;
use Zend\Text\Table;
use Zend\Console\Request as ConsoleRequest;

/**
 * For CLI or E-Mail useful
 */
class ZendTable extends AbstractRenderer
{

    /**
     * In windows...
     *
     * @var integer
     */
    private $maxConsoleWidth = 78;

    public function getName ()
    {
        return 'zendTable';
    }
    

    public function isExport ()
    {
        return false;
    }
    
    public function isHtml ()
    {
        return false;
    }

    /**
     *
     * @todo enable parameters from console
     *      
     * @return array
     */
    public function getSortConditions ()
    {
        $request = $this->getRequest();
        if (! $request instanceof ConsoleRequest) {
            throw new \Exception('Must be an instance of ConsoleRequest for console rendering');
        }
        
        $rendererOptions = $this->getRendererOptions();
        $parameterNames = $rendererOptions['parameterNames'];
        
        $sortConditions = array();
        
        $sortColumns = $request->getParam($parameterNames['sortColumns']);
        $sortDirections = $request->getParam($parameterNames['sortDirections']);
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

    /**
     *
     * @todo enable parameters from console
     *      
     * @return array
     */
    public function getFilters ()
    {
        $request = $this->getRequest();
        if (! $request instanceof ConsoleRequest) {
            throw new \Exception('Must be an instance of ConsoleRequest for console rendering');
        }
        
        return array();
    }

    /**
     * Should be implemented for each renderer itself (just default)
     *
     * @return integer
     */
    public function getCurrentPageNumber ()
    {
        $request = $this->getRequest();
        if (! $request instanceof ConsoleRequest) {
            throw new \Exception('Must be an instance of ConsoleRequest for console rendering');
        }
        
        $rendererOptions = $this->getRendererOptions();
        $parameterNames = $rendererOptions['parameterNames'];
        if ($request->getParam($parameterNames['currentPage']) != '') {
            return (int) $request->getParam($parameterNames['currentPage']);
        }
        
        return (int) 1;
    }

    public function getItemsPerPage ($defaultItems = 25)
    {
        $request = $this->getRequest();
        if (! $request instanceof ConsoleRequest) {
            throw new \Exception('Must be an instance of ConsoleRequest for console rendering');
        }
        
        $rendererOptions = $this->getRendererOptions();
        $parameterNames = $rendererOptions['parameterNames'];
        if ($request->getParam($parameterNames['itemsPerPage']) != '') {
            return (int) $request->getParam($parameterNames['itemsPerPage']);
        }
        
        return (int) $defaultItems;
    }


    public function execute ()
    {
        $viewModel = $this->getViewModel();
        
        $textTable = $this->getTable();
        
        $response = $this->getMvcEvent()->getResponse();
        $response->setContent($textTable);
        
        return $response;
    }

    /**
     *
     * @return \Zend\Text\Table\Table
     */
    private function getTable ()
    {
        $paginator = $this->getPaginator();
        $translator = $this->getTranslator();
        
        $options = array(
            'columnWidths' => $this->getColumnWidth()
        );
        
        $table = new TextTable($options);
        $table->setDecorator('ascii');
        // $table->setAutoSeparate(TextTable::AUTO_SEPARATE_HEADER);
        
        /**
         * Title
         */
        $tableRow = new Table\Row();
        
        $tableColumn = new Table\Column($this->getTitle());
        $tableColumn->setColSpan(count($options['columnWidths']));
        $tableColumn->setAlign(Table\Column::ALIGN_CENTER);
        $tableRow->appendColumn($tableColumn);
        
        $table->appendRow($tableRow);
        
        /**
         * Header
         */
        $tableRow = new Table\Row();
        foreach ($this->getColumns() as $column) {
            if (! $column->isHidden()) {
                $label = $column->getLabel();
                if ($this->getTranslator() !== null) {
                    $label = $this->getTranslator()->translate($label);
                }
                if (function_exists('mb_strtoupper')) {
                    $label = mb_strtoupper($label);
                } else {
                    $label = strtoupper($label);
                }
                
                $tableColumn = new Table\Column($label);
                if ($column->getType() instanceof Type\Number) {
                    $tableColumn->setAlign(Table\Column::ALIGN_RIGHT);
                } else {
                    $tableColumn->setAlign(Table\Column::ALIGN_LEFT);
                }
                
                $tableRow->appendColumn($tableColumn);
            }
        }
        $table->appendRow($tableRow);
        
        /**
         * Data
         */
        foreach ($this->getData() as $row) {
            $tableRow = new Table\Row();
            
            foreach ($this->getColumns() as $column) {
                if (! $column->isHidden()) {
                    $value = '';
                    if (isset($row[$column->getUniqueId()]))
                        $value = $row[$column->getUniqueId()];
                    
                    $tableColumn = new Table\Column($value);
                    if ($column->getType() instanceof Type\Number) {
                        $tableColumn->setAlign(Table\Column::ALIGN_RIGHT);
                    } else {
                        $tableColumn->setAlign(Table\Column::ALIGN_LEFT);
                    }
                    $tableRow->appendColumn($tableColumn);
                }
            }
            
            $table->appendRow($tableRow);
        }
        
        /**
         * Pagination
         */
        $tableRow = new Table\Row();
        
        $footer = $translator->translate('Page') . ' ';
        $footer .= $paginator->getCurrentPageNumber() . ' ' . $translator->translate('of') . ' ' . $paginator->count();
        
        $footer .= ' / ';
        
        $footer .= $translator->translate('Showing') . ' ' . $paginator->getCurrentItemCount() . ' ' . $translator->translate('of') . ' ' . $paginator->getTotalItemCount() . ' ' . $translator->translate('items');
        
        $tableColumn = new Table\Column($footer);
        $tableColumn->setColSpan(count($options['columnWidths']));
        $tableColumn->setAlign(Table\Column::ALIGN_CENTER);
        $tableRow->appendColumn($tableColumn);
        
        $table->appendRow($tableRow);
        
        return $table;
    }

    private function getColumnWidth ()
    {
        $return = array();
        
        $displayColumns = array();
        foreach ($this->getColumns() as $column) {
            if (! $column->isHidden()) {
                $displayColumns[] = 1;
            }
        }
        
        $maxWidth = $this->maxConsoleWidth - count($displayColumns);
        $oneColWidth = floor($maxWidth / count($displayColumns));
        foreach ($displayColumns as &$col) {
            $return[] = (int) $oneColWidth * $col;
        }
        
        $i = 0;
        while (array_sum($return) < $maxWidth) {
            $return[$i] = $return[$i] + 1;
            
            $i ++;
        }
        
        return $return;
    }
}
