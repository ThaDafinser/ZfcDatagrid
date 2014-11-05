<?php
namespace ZfcDatagrid\Renderer\JqGrid\View\Helper;

use ZfcDatagrid\Filter;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class Columns extends AbstractHelper implements ServiceLocatorAwareInterface
{
    private $translator;

    const STYLE_BOLD = 'cellvalue = \'<span style="font-weight: bold;">\' + cellvalue + \'</span>\';';

    const STYLE_ITALIC = 'cellvalue = \'<span style="font-style: italic;">\' + cellvalue + \'</span>\';';

    /**
     * Set the service locator.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return CustomHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get the service locator.
     *
     * @return \Zend\View\HelperPluginManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @param  string $message
     * @return string
     */
    private function translate($message)
    {
        if (false === $this->translator) {
            return $message;
        }

        if (null === $this->translator) {
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

    /**
     *
     * @param  array  $columns
     * @return string
     */
    public function __invoke(array $columns)
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
                'search' => (bool) $column->isUserFilterEnabled(),
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
            $rendererParameters = $column->getRendererParameters('jqGrid');
            if (isset($rendererParameters['cellattr'])) {
                $options['cellattr'] = (string) $rendererParameters['cellattr'];
            }

            /**
             * Filtering
             */
            $searchoptions = array();
            $searchoptions['clearSearch'] = false;
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
                    if (true === $value) {
                        $value = 'true';
                    } else {
                        $value = 'false';
                    }
                } elseif ('formatter' == $key) {
                    if (stripos($value, 'formatter') === false && stripos($value, 'function') === false) {
                        $value = '"'.$value.'"';
                    }
                } elseif ('cellattr' == $key) {
                    // SKIP THIS
                } else {
                    $value = '"'.$value.'"';
                }

                $colModel[] = (string) $key.': '.$value;
            }

            $return[] = '{'.implode(',', $colModel).'}';
        }

        return '['.implode(',', $return).']';
    }

    /**
     *
     * @param  Column\AbstractColumn $column
     * @return string
     */
    private function getFormatter(Column\AbstractColumn $column)
    {
        /*
         * User defined formatter
         */
        $rendererParameters = $column->getRendererParameters('jqGrid');
        if (isset($rendererParameters['formatter'])) {
            return $rendererParameters['formatter'];
        }

        /*
         * Formatter based on column options + styles
         */
        $formatter = '';

        $formatter .= implode(' ', $this->getStyles($column));

        switch (get_class($column->getType())) {

            case 'ZfcDatagrid\Column\Type\PhpArray':
                $formatter .= 'cellvalue = \'<pre>\' + cellvalue + \'</pre>\';';
                break;
        }

        if ($column instanceof Column\Action) {
            $formatter .= ' cellvalue = cellvalue; ';
        }

        if ($formatter != '') {
            $prefix = 'function (cellvalue, options, rowObject) {';
            $suffix = ' return cellvalue; }';

            $formatter = $prefix.$formatter.$suffix;
        }

        return $formatter;
    }

    /**
     *
     * @param  Column\AbstractColumn $col
     * @throws \Exception
     * @return array
     */
    private function getStyles(Column\AbstractColumn $col)
    {
        $styleFormatter = array();

        /*
         * First all based on value (only one works) @todo
         */
        foreach ($col->getStyles() as $style) {
            $prepend = '';
            $append = '';

            /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
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
                        throw new \Exception('Currently not supported filter operation: "'.$rule['operator'].'"');
                        break;
                }

                $prepend = 'if (rowObject.'.$colString.' '.$operator.' \''.$rule['value'].'\') {';
                $append .= '}';
            }

            $styleString = '';
            switch (get_class($style)) {

                case 'ZfcDatagrid\Column\Style\Bold':
                    $styleString = self::STYLE_BOLD;
                    break;

                case 'ZfcDatagrid\Column\Style\Italic':
                    $styleString = self::STYLE_ITALIC;
                    break;

                case 'ZfcDatagrid\Column\Style\Color':
                    $styleString = 'cellvalue = \'<span style="color: #'.$style->getRgbHexString().';">\' + cellvalue + \'</span>\';';
                    break;

                case 'ZfcDatagrid\Column\Style\BackgroundColor':
                    // do NOTHING! this is done by loadComplete event...
                    // At this stage jqgrid haven't created the columns...
                    break;

                default:
                    throw new \Exception('Not defined style: "'.get_class($style).'"');
                    break;
            }

            $styleFormatter[] = $prepend.$styleString.$append;
        }

        return $styleFormatter;
    }
}
