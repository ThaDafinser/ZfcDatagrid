<?php

namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Action\AbstractAction;

/**
 * View Helper.
 */
class TableRow extends AbstractHelper
{
    /** @var \Zend\I18n\Translator\Translator|null|false */
    private $translator;

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
     * @param $row
     * @param bool|true $open
     *
     * @return string
     */
    private function getTr($row, $open = true)
    {
        if ($open !== true) {
            return '</tr>';
        } else {
            if (isset($row['idConcated'])) {
                return '<tr id="'.$row['idConcated'].'">';
            } else {
                return '<tr>';
            }
        }
    }

    /**
     * @param $dataValue
     * @param array $attributes
     *
     * @return string
     */
    private function getTd($dataValue, $attributes = [])
    {
        $attr = [];
        foreach ($attributes as $name => $value) {
            if ($value != '') {
                $attr[] = $name.'="'.$value.'"';
            }
        }

        $attr = implode(' ', $attr);

        return '<td '.$attr.'>'.$dataValue.'</td>';
    }

    /**
     * @param array          $row
     * @param array          $cols
     * @param AbstractAction $rowClickAction
     * @param array          $rowStyles
     *
     * @throws \Exception
     *
     * @return string
     */
    public function __invoke($row, array $cols, AbstractAction $rowClickAction = null, array $rowStyles = [], $hasMassActions = false)
    {
        $return = $this->getTr($row);

        if (true === $hasMassActions) {
            $return .= '<td><input type="checkbox" name="massActionSelected[]" value="'.$row['idConcated'].'" /></td>';
        }

        foreach ($cols as $col) {
            /* @var $col Column\AbstractColumn */

            $value = $row[$col->getUniqueId()];

            $cssStyles = [];
            $classes = [];

            if ($col->isHidden() === true) {
                $classes[] = 'hidden';
            }

            switch (get_class($col->getType())) {

                case Column\Type\Number::class:
                    $cssStyles[] = 'text-align: right';
                    break;

                case Column\Type\PhpArray::class:
                    $value = '<pre>'.print_r($value, true).'</pre>';
                    break;
            }

            $styles = array_merge($rowStyles, $col->getStyles());
            foreach ($styles as $style) {
                /* @var $style Column\Style\AbstractStyle */
                if ($style->isApply($row) === true) {
                    switch (get_class($style)) {

                        case Column\Style\Bold::class:
                            $cssStyles[] = 'font-weight: bold';
                            break;

                        case Column\Style\Italic::class:
                            $cssStyles[] = 'font-style: italic';
                            break;

                        case Column\Style\Color::class:
                            $cssStyles[] = 'color: #'.$style->getRgbHexString();
                            break;

                        case Column\Style\BackgroundColor::class:
                            $cssStyles[] = 'background-color: #'.$style->getRgbHexString();
                            break;

                        case Column\Style\Align::class:
                            $cssStyles[] = 'text-align: '.$style->getAlignment();
                            break;

                        case Column\Style\Strikethrough::class:
                            $value = '<s>'.$value.'</s>';
                            break;

                        case Column\Style\CSSClass::class:
                            $classes[] = $style->getClass();
                            break;

                        case Column\Style\Html::class:
                            // do NOTHING! just pass the HTML!
                            break;

                        default:
                            throw new \InvalidArgumentException('Not defined style: "'.get_class($style).'"');
                            break;
                    }
                }
            }

            if ($col instanceof Column\Action) {
                /* @var $col Column\Action */
                $actions = [];
                foreach ($col->getActions() as $action) {
                    /* @var $action Column\Action\AbstractAction */
                    if ($action->isDisplayed($row) === true) {
                        $action->setTitle($this->translate($action->getTitle()));
                        $actions[] = $action->toHtml($row);
                    }
                }

                $value = implode(' ', $actions);
            }

            // "rowClick" action
            if ($col instanceof Column\Select && $rowClickAction instanceof AbstractAction
                    && $col->isRowClickEnabled()) {
                $value = '<a href="'.$rowClickAction->getLinkReplaced($row).'">'.$value.'</a>';
            }

            $attributes = [
                'class' => implode(' ', $classes),
                'style' => implode(';', $cssStyles),
                'data-columnUniqueId' => $col->getUniqueId(),
            ];

            $return .= $this->getTd($value, $attributes);
        }

        $return .= $this->getTr($row, false);

        return $return;
    }
}
