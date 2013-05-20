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
            
            $styles = array();
            $classes = array();
            
            if ($column->isHidden() === true) {
                $classes[] = 'hidden';
            }
            
            $type = $column->getType();
            if ($type instanceof Type\Number) {
                $styles[] = 'text-align: right';
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
                $value = ' <a href="#" class="thumbnail"><img src="' . $row[$column->getUniqueId()] . '" /></a>';
            } elseif ($column instanceof Column\Action) {
                $actions = array();
                foreach ($column->getActions() as $action) {
                    $icon = '';
                    if ($action->hasIconClass() === true) {
                        $icon = '<i class="' . $action->getIconClass() . '"></i> ';
                    }
                    $actions[] = '<a class="btn" href="' . $action->getLink() . '">' . $icon . $action->getLabel() . '</a>';
                }
                
                $value = implode(' ', $actions);
            }  else {
                $value = $row[$column->getUniqueId()];
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
