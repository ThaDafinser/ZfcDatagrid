<?php
namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Action\AbstractAction;

/**
 * View Helper
 */
class TableRow extends AbstractHelper
{

    private function getTr($row, $open = true)
    {
        if ($open !== true) {
            return '</tr>';
        } else {
            if (isset($row['idConcated']))
                return '<tr id="' . $row['idConcated'] . '">';
            else
                return '<tr>';
        }
    }

    private function getTd($dataValue, $attributes = array())
    {
        $attr = array();
        foreach ($attributes as $name => $value) {
            if ($value != '') {
                $attr[] = $name . '="' . $value . '"';
            }
        }
        
        $attr = implode(' ', $attr);
        
        return '<td ' . $attr . '>' . $dataValue . '</td>';
    }

    public function __invoke($row, $columns, AbstractAction $rowClickAction = null)
    {
        $return = $this->getTr($row);
        
        foreach ($columns as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            $value = $row[$column->getUniqueId()];
            
            $styles = array();
            $classes = array();
            
            if ($column->isHidden() === true) {
                $classes[] = 'hidden';
            }
            
            switch (get_class($column->getType())) {
                
                case 'ZfcDatagrid\Column\Type\Number':
                    $styles[] = 'text-align: right';
                    break;
                
                case 'ZfcDatagrid\Column\Type\PhpArray':
                    $value = '<pre>' . print_r($value, true) . '</pre>';
                    break;
                
                case 'ZfcDatagrid\Column\Type\Image':
                    // if thumb and original provided
                    $thumb = $value;
                    $original = $value;
                    if (is_array($value)) {
                        $thumb = $value[0];
                        $original = $value[0];
                        if (array_key_exists(1, $value)) {
                            $original = $value[1];
                        }
                    }
                    $value = ' <a href="' . $original . '" class="thumbnail"><img src="' . $thumb . '" /></a>';
                    break;
            }
            
            if ($column->hasStyles() === true) {
                foreach ($column->getStyles() as $style) {
                    /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
                    if ($style->isApply($row) === true) {
                        
                        switch (get_class($style)) {
                            
                            case 'ZfcDatagrid\Column\Style\Bold':
                                $styles[] = 'font-weight: bold';
                                break;
                            case 'ZfcDatagrid\Column\Style\Italic':
                                $styles[] = 'font-style: italic';
                                break;
                            
                            case 'ZfcDatagrid\Column\Style\Color':
                                $styles[] = 'color: #' . $style->getRgbHexString();
                                break;
                            
                            default:
                                throw new \Exception('Not defined yet: "' . get_class($style) . '"');
                                
                                break;
                        }
                    }
                }
            }
            
            if ($column instanceof Column\Action) {
                /* @var $column \ZfcDatagrid\Column\Action */
                $actions = array();
                foreach ($column->getActions() as $action) {
                    /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
                    
                    if ($action->isDisplayed($row) === true) {
                        $actions[] = $action->toHtml($row);
                    }
                }
                
                $value = implode(' ', $actions);
            }
            
            // "rowClick" action
            if ($column instanceof Column\Select && $rowClickAction instanceof AbstractAction) {
                $value = '<a href="' . $rowClickAction->getLinkReplaced($row) . '">' . $value . '</a>';
            }
            
            $attributes = array(
                'class' => implode(',', $classes),
                'style' => implode(';', $styles),
                'data-columnUniqueId' => $column->getUniqueId()
            );
            
            $return .= $this->getTd($value, $attributes);
        }
        
        $return .= $this->getTr($row, false);
        
        return $return;
    }
}
