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

    public function __invoke ($row, $columns, AbstractAction $rowClickAction = null)
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
            
            if ($column instanceof Column\Image) {
	            /* @var Column\Image $column */
                $value = ' <a href="#" class="thumbnail">
                    <img src="' . $value . '" ' . $column->getImageStyleTag() . ' />
                </a>';
            } elseif ($column instanceof Column\Action) {
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
            
            if ($column instanceof Column\Standard && $rowClickAction instanceof AbstractAction) {
                $value = '<a href="' . $rowClickAction->getLinkReplaced($row) . '">' . $value . '</a>';
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
