<?php

namespace ZfcDatagrid\Renderer\JqGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Filter;

/**
 * View Helper.
 */
class Columns extends AbstractHelper
{
    /** @var \Zend\I18n\Translator\Translator|null|false */
    private $translator;

    const STYLE_BOLD = 'cellvalue = \'<span style="font-weight: bold;">\' + cellvalue + \'</span>\';';

    const STYLE_ITALIC = 'cellvalue = \'<span style="font-style: italic;">\' + cellvalue + \'</span>\';';

    const STYLE_STRIKETHROUGH = 'cellvalue = \'<span style="text-decoration: line-through;">\' + cellvalue + \'</span>\';';

    /**
     * @param false|null|\Zend\I18n\Translator\Translator $translator
     *
     * @return self
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return string
     */
    private function translate($message)
    {
        if (null === $this->translator) {
            return $message;
        }

        return $this->translator->translate($message);
    }

    /**
     * @param array $columns
     *
     * @return string
     */
    public function __invoke(array $columns)
    {
        $return = [];

        foreach ($columns as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */

            $options = [
                'name' => (string) $column->getUniqueId(),
                'index' => (string) $column->getUniqueId(),
                'label' => $this->translate((string) $column->getLabel()),

                'width' => $column->getWidth(),
                'hidden' => (bool) $column->isHidden(),
                'sortable' => (bool) $column->isUserSortEnabled(),
                'search' => (bool) $column->isUserFilterEnabled(),
            ];

            /*
             * Formatting
             */
            $formatter = $this->getFormatter($column);
            if ($formatter != '') {
                $options['formatter'] = (string) $formatter;
            }

            $alignAlreadyDefined = false;
            if ($column->hasStyles()) {
                foreach ($column->getStyles() as $style) {
                    /** @var \ZfcDatagrid\Column\Style\Align $style */
                    if (get_class($style) == 'ZfcDatagrid\Column\Style\Align') {
                        $options['align'] = $style->getAlignment();
                        $alignAlreadyDefined = true;
                        break;
                    }
                }
            }

            if (!$alignAlreadyDefined && $column->getType() instanceof Type\Number) {
                $options['align'] = Column\Style\Align::$RIGHT;
            }

            /*
             * Cellattr
             */
            $rendererParameters = $column->getRendererParameters('jqGrid');
            if (isset($rendererParameters['cellattr'])) {
                $options['cellattr'] = (string) $rendererParameters['cellattr'];
            }
            if (isset($rendererParameters['classes'])) {
                $options['classes'] = (string) $rendererParameters['classes'];
            }

            /*
             * Filtering
             */
            $searchoptions = [];
            $searchoptions['clearSearch'] = false;
            if ($column->hasFilterSelectOptions() === true) {
                $options['stype'] = 'select';
                $searchoptions['value'] = $column->getFilterSelectOptions();

                if ($column->hasFilterDefaultValue() === true) {
                    $searchoptions['defaultValue'] = $column->getFilterDefaultValue();
                } else {
                    $searchoptions['defaultValue'] = '';
                }
            } elseif ($column->hasFilterDefaultValue() === true) {
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
            $colModel = [];
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
     * @param Column\AbstractColumn $column
     *
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
                $formatter .= 'cellvalue = \'<pre>\' + cellvalue.join(\'<br />\') + \'</pre>\';';
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
     * @param Column\AbstractColumn $col
     *
     * @throws \Exception
     *
     * @return array
     */
    private function getStyles(Column\AbstractColumn $col)
    {
        $styleFormatter = [];

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

                case 'ZfcDatagrid\Column\Style\Strikethrough':
                    $styleString = self::STYLE_STRIKETHROUGH;
                    break;

                case 'ZfcDatagrid\Column\Style\Color':
                    $styleString = 'cellvalue = \'<span style="color: #'.$style->getRgbHexString().';">\' + cellvalue + \'</span>\';';
                    break;

                case 'ZfcDatagrid\Column\Style\CSSClass':
                    $styleString = 'cellvalue = \'<span class="'.$style->getClass().'">\' + cellvalue + \'</span>\';';
                    break;

                case 'ZfcDatagrid\Column\Style\BackgroundColor':
                    // do NOTHING! this is done by loadComplete event...
                    // At this stage jqgrid haven't created the columns...
                    break;

                case 'ZfcDatagrid\Column\Style\Html':
                    // do NOTHING! just pass the HTML!
                    break;

                case 'ZfcDatagrid\Column\Style\Align':
                    // do NOTHING! we have to add the align style in the gridcell and not in a span!
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
