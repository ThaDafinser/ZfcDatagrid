<?php
namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Type;

/**
 * View Helper
 */
class TableRow extends AbstractHelper
{

    private function getTr ($row, $open = true)
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

    private function getTd ($dataValue, $attributes = array())
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

    public function __invoke ($row, $columns, $rowClickLink)
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
            
            switch ($column->getType()->getTypeName()) {
                case 'number':
                    $styles[] = 'text-align: right';
                    break;
                
                case 'array':
                    $value = '<pre>' . print_r($value, true) . '</pre>';
                    break;
            }
            
            if ($column->hasStyles() === true) {
                foreach ($column->getStyles() as $style) {
                    /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
                    if ($style->isApply($row) === true) {
                        if ($style instanceof Style\Bold) {
                            $styles[] = 'font-weight: bold';
                        } elseif ($style instanceof Style\Color\Red) {
                            $styles[] = 'color: red';
                        } else {
                            throw new \Exception('Not defined yet: "' . get_class($style) . '"');
                        }
                    }
                }
            }
            
            if ($column instanceof Column\Image) {
                $value = ' <a href="#" class="thumbnail"><img src="' . $value . '" /></a>';
            } elseif ($column instanceof Column\Action) {
                /* @var $column \ZfcDatagrid\Column\Action */
                $actions = array();
                foreach ($column->getActions() as $action) {
                    /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
                    
                    if ($action->isDisplayed($row) === true) {
                        $actions[] = $action->toHtml();
                    }
                }
                
                $value = implode(' ', $actions);
            }
            
            if ($column instanceof Column\Standard && $rowClickLink != '#') {
                $value = '<a href="' . $rowClickLink . '">' . $value . '</a>';
            }
            
            $attributes = array(
                'class' => implode(',', $classes),
                'style' => implode(';', $styles)
            );
            
            $return .= $this->getTd($value, $attributes);
        }
        
        $return .= $this->getTr($row, false);
        
        return $return;
    }
}
