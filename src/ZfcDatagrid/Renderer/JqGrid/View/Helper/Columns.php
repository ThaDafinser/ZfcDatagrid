<?php
namespace ZfcDatagrid\Renderer\JqGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Type;

/**
 * View Helper
 */
class Columns extends AbstractHelper
{

    public function __invoke (array $columns)
    {
        $return = array();
        
        foreach ($columns as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            $options = array(
                'name' => $column->getUniqueId(),
                'index' => $column->getUniqueId(),
                'label' => $column->getLabel(),
                
                'hidden' => $column->isHidden(),
                'sortable' => $column->isUserSortEnabled(),
                'search' => $column->isUserFilterEnabled()
            );
            
            /**
             * Formatting
             */
            $formatter = '';
            switch ($column->getType()->getTypeName()) {
                // Numbers + Date are already formatted on the server side!
                case 'email':
                    $formatter = 'email';
                    break;
                
                // case 'link':
                // $formatter = 'link';
                // break;
            }
            if ($formatter != '') {
                $options['formatter'] = $formatter;
            }
            
            if ($column->getType() instanceof Type\Number) {
                $options['align'] = 'right';
            }
            
            /**
             * Filtering
             */
            $searchoptions = array();
            if ($column->hasFilterSelectOptions() === true) {
                $options['stype'] = 'select';
                $searchoptions['value'] = $column->getFilterSelectOptions();
            }
            
            if ($column->hasFilterDefaultValue() === true) {
                $filter = new \ZfcDatagrid\Filter();
                $filter->setFromColumn($column, $column->getFilterDefaultValue());
                
                $searchoptions['defaultValue'] = $filter->getDisplayValue();
            }
            
            if (count($searchoptions) > 0) {
                $options['searchoptions'] = $searchoptions;
            }
            
            $return[] = $options;
        }
        
        return json_encode($return);
    }
}