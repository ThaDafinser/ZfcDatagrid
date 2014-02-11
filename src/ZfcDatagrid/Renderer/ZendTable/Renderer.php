<?php
namespace ZfcDatagrid\Renderer\ZendTable;

use ZfcDatagrid\Renderer\AbstractRenderer;
use ZfcDataGrid\Column\Type;
use ZfcDatagrid\Column;
use Zend\Text\Table\Table as TextTable;
use Zend\Text\Table;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Console;

/**
 * For CLI or E-Mail useful
 */
class Renderer extends AbstractRenderer
{

    /**
     * In windows...
     *
     * @var integer
     */
    private $consoleWidth;

    private $columnsToDisplay;

    public function getName()
    {
        return 'zendTable';
    }

    public function isExport()
    {
        return false;
    }

    public function isHtml()
    {
        return false;
    }

    /**
     *
     * @return ConsoleRequest
     */
    public function getRequest()
    {
        $request = parent::getRequest();
        if (! $request instanceof ConsoleRequest) {
            throw new \Exception('Request must be an instance of Zend\Console\Request for console rendering');
        }
        
        return $request;
    }

    /**
     *
     * @todo enable parameters from console
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
    public function getFilters()
    {
        $request = $this->getRequest();
        
        return array();
    }

    /**
     * Should be implemented for each renderer itself (just default)
     *
     * @return integer
     */
    public function getCurrentPageNumber()
    {
        $request = $this->getRequest();
        
        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];
        if ($request->getParam($parameterNames['currentPage']) != '') {
            return (int) $request->getParam($parameterNames['currentPage']);
        }
        
        return (int) 1;
    }

    public function getItemsPerPage($defaultItems = 25)
    {
        $request = $this->getRequest();
        
        $optionsRenderer = $this->getOptionsRenderer();
        $parameterNames = $optionsRenderer['parameterNames'];
        if ($request->getParam($parameterNames['itemsPerPage']) != '') {
            return (int) $request->getParam($parameterNames['itemsPerPage']);
        }
        
        return (int) $defaultItems;
    }

    public function execute()
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
    private function getTable()
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
        foreach ($this->getColumnsToDisplay() as $column) {
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
        $table->appendRow($tableRow);
        
        /**
         * Data
         */
        foreach ($this->getData() as $row) {
            $tableRow = new Table\Row();
            
            foreach ($this->getColumnsToDisplay() as $column) {
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

    /**
     * Decide which columns we want to display
     *
     * @return Column\AbstractColumn[]
     */
    private function getColumnsToDisplay()
    {
        if (is_array($this->columnsToDisplay)) {
            return $this->columnsToDisplay;
        }
        
        $columnsToDisplay = array();
        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            if (! $column instanceof Column\Action && $column->isHidden() === false) {
                $columnsToDisplay[] = $column;
            }
        }
        if (count($columnsToDisplay) === 0) {
            throw new \Exception('No columns to di available');
        }
        
        $this->columnsToDisplay = $columnsToDisplay;
        
        return $this->columnsToDisplay;
    }

    /**
     *
     * @return array
     */
    private function getColumnWidth()
    {
        $return = array();
        
        $maxWidth = $this->getWidthAvailable() - count($this->getColumnsToDisplay());
        $oneColWidth = floor($maxWidth / count($this->getColumnsToDisplay()));
        foreach ($this->getColumnsToDisplay() as $col) {
            $return[] = (int) $oneColWidth * $col;
        }
        
        $i = 0;
        while (array_sum($return) < $maxWidth) {
            $return[$i] = $return[$i] + 1;
            
            $i ++;
        }
        
        return $return;
    }

    /**
     * Get the console width
     *
     * @return number
     */
    private function getWidthAvailable()
    {
        if ($this->consoleWidth !== null) {
            return $this->consoleWidth;
        }
        
        $console = Console::getInstance();
        // Minus 2, because of the table!
        $this->consoleWidth = $console->getWidth() - 2;
        
        return $this->consoleWidth;
    }
}
