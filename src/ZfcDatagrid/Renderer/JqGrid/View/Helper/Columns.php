<?php
namespace ZfcDatagrid\Renderer\JqGrid\View\Helper;

use ZfcDatagrid\Filter;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcDatagrid\Column\Style\Color\AbstractColor;

/**
 * View Helper
 */
class Columns extends AbstractHelper implements ServiceLocatorAwareInterface
{

    private $translator;

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator            
     * @return CustomHelper
     */
    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the service locator.
     *
     * @return \Zend\View\HelperPluginManager
     */
    public function getServiceLocator ()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @param string $message            
     * @return string
     */
    public function translate ($message)
    {
        if ($this->translator === false) {
            return $message;
        }
        
        if ($this->translator === null) {
            if ($this->getServiceLocator()
                ->getServiceLocator()
                ->has('translator')) {
                $this->translator = $this->getServiceLocator()
                    ->getServiceLocator()
                    ->get('translator');
            } else {
                $this->translator = false;
                return $message;
            }
        }
        
        return $this->translator->translate($message);
    }

    public function __invoke (array $columns)
    {
        $return = array();
        
        foreach ($columns as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            $options = array(
                'name' => (string) $column->getUniqueId(),
                'index' => (string) $column->getUniqueId(),
                'label' => $this->translate((string) $column->getLabel()),
                
                'width' => $column->getWidth(),
                'hidden' => (bool) $column->isHidden(),
                'sortable' => (bool) $column->isUserSortEnabled(),
                'search' => (bool) $column->isUserFilterEnabled()
            );
            
            /**
             * Formatting
             */
            $formatter = $this->getFormatter($column);
            if ($formatter != '') {
                $options['formatter'] = (string) $formatter;
            }
            
            if ($column->getType() instanceof Type\Number) {
                $options['align'] = (string) 'right';
            }
            
            /**
             * Cellattr
             */
            $rendererParameters = $column->getRendererParameters('jqgrid');
            if (isset($rendererParameters['cellattr'])) {
                $options['cellattr'] = (string) $rendererParameters['cellattr'];
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
                
                $searchoptions['defaultValue'] = $filter->getDisplayColumnValue();
            }
            
            if (count($searchoptions) > 0) {
                $options['searchoptions'] = $searchoptions;
            }
            
            /**
             * Because with json_encode we get problems, it's custom made!
             */
            $colModel = array();
            foreach ($options as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                } elseif (is_bool($value)) {
                    if ($value === true) {
                        $value = 'true';
                    } else {
                        $value = 'false';
                    }
                } elseif ($key == 'formatter') {
                    if (stripos($value, 'formatter') === false && stripos($value, 'function') === false) {
                        $value = '"' . $value . '"';
                    }
                } elseif ($key == 'cellattr') {
                    // SKIP THIS
                } else {
                    $value = '"' . $value . '"';
                }
                
                $colModel[] = (string) $key . ': ' . $value;
            }
            
            $return[] = '{' . implode(',', $colModel) . '}';
        }
        
        return '[' . implode(',', $return) . ']';
    }

    private function getFormatter ($column)
    {
        /*
         * User defined formatter
         */
        $rendererParameters = $column->getRendererParameters('jqgrid');
        if (isset($rendererParameters['formatter'])) {
            return $rendererParameters['formatter'];
        }
        
        /*
         * Formatter based on column options + styles
         */
        $formatter = '';
        
        if ($column->hasStyles() === true) {
            $styleFormatter = array();
            
            /*
             * First all based on value (only one works) @todo
             */
            foreach ($column->getStyles() as $style) {
                /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
                if ($style->isForAll() === false) {
                    foreach ($style->getByValues() as $rule) {
                        $colString = $rule['column']->getUniqueId();
                        $operator = '';
                        switch ($rule['operator']) {
                            
                            case Filter::EQUAL:
                                $operator = '==';
                                break;
                            
                            case Filter::NOT_EQUAL:
                                $operator = '!=';
                                break;
                            
                            default:
                                throw new \Exception('currently not implemented filter type: "' . $rule['operator'] . '"');
                                break;
                        }
                        
                        $styleString = 'if(rowObject.' . $colString . ' ' . $operator . ' \'' . $rule['value'] . '\'){';

                        switch (get_class($style)) {
                            
                            case 'ZfcDatagrid\Column\Style\Bold':
                                $styleString .= 'cellvalue = \'<span style="font-weight: bold;">\' + cellvalue + \'</span>\';';
                                break;
                            case 'ZfcDatagrid\Column\Style\Italic':
                                $styleString .= 'cellvalue = \'<span style="font-weight: italic;">\' + cellvalue + \'</span>\';';
                                break;
                            
                            case 'ZfcDatagrid\Column\Style\Color':
                                $styleString .= 'cellvalue = \'<span style="color: #' . $style->getRgbHexString() . ';">\' + cellvalue + \'</span>\';';
                                break;
                            
                            default:
                                throw new \Exception('Not defined yet: "' . get_class($style) . '"');
                                
                                break;
                        }
                        
                        $styleString .= '}';
                        
                        $styleFormatter[] = $styleString;
                    }
                }
            }
            
            foreach ($column->getStyles() as $style) {
                /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
                if ($style->isForAll() === true) {
                    if ($style instanceof Style\Bold) {
                        $styleFormatter[] = 'cellvalue = \'<span style="font-weight: bold;">\' + cellvalue + \'</span>\';';
                    } elseif ($style instanceof Style\Color\Red) {
                        $styleFormatter[] = 'cellvalue = \'<span style="color: red;">\' + cellvalue + \'</span>\';';
                    } else {
                        throw new \Exception('Not defined yet: "' . get_class($style) . '"');
                    }
                }
            }
            
            $formatter .= implode(' ', $styleFormatter);
        }
        
        switch ($column->getType()->getTypeName()) {
            
            // Numbers + Date are already formatted on the server side!
            case 'email':
                $formatter .= 'cellvalue = \'<a href="mailto:\' + cellvalue\'">\' + cellvalue + \'</a>\';';
                break;
            
            case 'array':
                $formatter .= 'cellvalue = \'<pre>\' + cellvalue + \'</pre>\';';
                break;
        }
        
        if ($column instanceof Column\Action) {
            $formatter .= ' cellvalue = cellvalue; ';
        } elseif ($column instanceof Column\Image) {
            $formatter .= ' cellvalue = \'<img src="\' + cellvalue + \'" />\'; ';
        } elseif ($column instanceof Column\Icon) {
            $formatter .= ' cellvalue = \'<i class="\' + cellvalue + \'" />\'; ';
        }
        
        if ($formatter != '') {
            $prefix = 'function(cellvalue, options, rowObject){';
            $suffix = ' return cellvalue; }';
            
            $formatter = $prefix . $formatter . $suffix;
        }
        
        return $formatter;
    }
}
