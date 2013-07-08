<?php
namespace ZfcDatagrid\Renderer\JqGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     *            @return CustomHelper
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
    public function translate($message){
        if($this->translator === false){
            return $message;
        }
        
        if($this->translator === null){
            if($this->getServiceLocator()->getServiceLocator()->has('translator')){
                $this->translator = $this->getServiceLocator()->getServiceLocator()->get('translator');
            } else{
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
            $formatter = '';
            switch ($column->getType()->getTypeName()) {
                // Numbers + Date are already formatted on the server side!
                case 'email':
                    $formatter = 'email';
                    break;
                
                case 'array':
                    $formatter = 'function(cellvalue, options, rowObject){';
                    // $formatter .= 'console.log(options); console.log(rowObject);';
                    $formatter .= 'return cellvalue;';
                    $formatter .= '}';
                    break;
                
                // case 'link':
                // $formatter = 'link';
                // break;
            }
            
            if ($column instanceof Column\Action) {
                $formatter = 'function(cellvalue, options, rowObject){';
                // $formatter .= 'console.log(options); console.log(rowObject);';
                $formatter .= 'return cellvalue;';
                $formatter .= '}';
            } elseif ($column instanceof Column\Image) {
                $formatter = 'function(cellvalue, options, rowObject){';
                $formatter .= 'return \'<img src="\' + cellvalue + \'" />\'';
                $formatter .= '}';
            }
            
            $rendererParameters = $column->getRendererParameters('jqgrid');
            if (isset($rendererParameters['formatter'])) {
                $formatter = $rendererParameters['formatter'];
            }
            
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
}
