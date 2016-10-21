<?php

namespace ZfcDatagrid\Column\Action;

use ZfcDatagrid\Column\AbstractColumn;

class Button extends AbstractAction
{
    protected $label = '';

    public function __construct()
    {
        parent::__construct();

        $this->addClass('btn');
        $this->addClass('btn-default');
    }

    /**
     * @param string|AbstractColumn $name
     */
    public function setLabel($name)
    {
        $this->label = $name;
    }

    /**
     * @return string|AbstractColumn
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    protected function getHtmlType()
    {
        throw new \Exception('not needed...since we have toHtml() here directly!');
    }

    /**
     * @param array $row
     *
     * @return string
     */
    public function toHtml(array $row)
    {
        if ($this->getLabel() == '') {
            throw new \InvalidArgumentException('A label is required for this action type, please call $action->setLabel()!');
        }

        $label = $this->getLabel();
        if ($label instanceof AbstractColumn) {
            $label = $row[$label->getUniqueId()];
        }

        return '<a '.$this->getAttributesString($row).'>'.$label.'</a>';
    }
}
