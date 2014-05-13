<?php
/**
 * Methods which can be used in (all) export renderer
 *
 */
namespace ZfcDatagrid\Renderer;

use ZfcDatagrid\Renderer\AbstractRenderer;
use ZfcDatagrid\Column;

abstract class AbstractExport extends AbstractRenderer
{

    protected $allowedColumnTypes = array(
        'ZfcDatagrid\Column\Type\DateTime',
        'ZfcDatagrid\Column\Type\Number',
        'ZfcDatagrid\Column\Type\PhpArray',
        'ZfcDatagrid\Column\Type\String'
    );

    /**
     * Decide which columns we want to display
     *
     * @return Column\AbstractColumn[]
     */
    protected function getColumnsToExport()
    {
        if (is_array($this->columnsToExport)) {
            return $this->columnsToExport;
        }
        
        $columnsToExport = array();
        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            if (! $column instanceof Column\Action && $column->isHidden() === false && in_array(get_class($column->getType()), $this->allowedColumnTypes)) {
                $columnsToExport[] = $column;
            }
        }
        if (count($columnsToExport) === 0) {
            throw new \Exception('No columns to export available');
        }
        
        $this->columnsToExport = $columnsToExport;
        
        return $this->columnsToExport;
    }
}
