<?php
namespace ZfcDatagrid\Renderer\BootstrapTable\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Action\AbstractAction;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class TableRow extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Set service locator
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @param  string $name
     * @return string
     */
    private function translate($name)
    {
        if ($this->getServiceLocator()->has('translator') === true) {
            return $this->getServiceLocator()
                ->get('translator')
                ->translate($name);
        }

        return $name;
    }

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

    private function getTd($dataValue, $attributes = array())
    {
        $attr = array();
        foreach ($attributes as $name => $value) {
            if ($value != '') {
                $attr[] = $name.'="'.$value.'"';
            }
        }

        $attr = implode(' ', $attr);

        return '<td '.$attr.'>'.$dataValue.'</td>';
    }

    /**
     *
     * @param  array          $row
     * @param  array          $cols
     * @param  AbstractAction $rowClickAction
     * @param  array          $rowStyles
     * @throws \Exception
     * @return string
     */
    public function __invoke($row, array $cols, AbstractAction $rowClickAction = null, array $rowStyles = array(), $hasMassActions = false)
    {
        $return = $this->getTr($row);

        if (true === $hasMassActions) {
            $return .= '<td><input type="checkbox" name="massActionSelected[]" value="'.$row['idConcated'].'" /></td>';
        }

        foreach ($cols as $col) {
            /* @var $col \ZfcDatagrid\Column\AbstractColumn */

            $value = $row[$col->getUniqueId()];

            $cssStyles = array();
            $classes = array();

            if ($col->isHidden() === true) {
                $classes[] = 'hidden';
            }

            switch (get_class($col->getType())) {

                case 'ZfcDatagrid\Column\Type\Number':
                    $cssStyles[] = 'text-align: right';
                    break;

                case 'ZfcDatagrid\Column\Type\PhpArray':
                    $value = '<pre>'.print_r($value, true).'</pre>';
                    break;
            }

            $styles = array_merge($rowStyles, $col->getStyles());
            foreach ($styles as $style) {
                /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
                if ($style->isApply($row) === true) {
                    switch (get_class($style)) {

                        case 'ZfcDatagrid\Column\Style\Bold':
                            $cssStyles[] = 'font-weight: bold';
                            break;

                        case 'ZfcDatagrid\Column\Style\Italic':
                            $cssStyles[] = 'font-style: italic';
                            break;

                        case 'ZfcDatagrid\Column\Style\Color':
                            $cssStyles[] = 'color: #'.$style->getRgbHexString();
                            break;

                        case 'ZfcDatagrid\Column\Style\BackgroundColor':
                            $cssStyles[] = 'background-color: #'.$style->getRgbHexString();
                            break;
                        default:
                            throw new \InvalidArgumentException('Not defined style: "'.get_class($style).'"');
                            break;
                    }
                }
            }

            if ($col instanceof Column\Action) {
                /* @var $col \ZfcDatagrid\Column\Action */
                $actions = array();
                foreach ($col->getActions() as $action) {
                    /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
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

            $attributes = array(
                'class' => implode(',', $classes),
                'style' => implode(';', $cssStyles),
                'data-columnUniqueId' => $col->getUniqueId(),
            );

            $return .= $this->getTd($value, $attributes);
        }

        $return .= $this->getTr($row, false);

        return $return;
    }
}
