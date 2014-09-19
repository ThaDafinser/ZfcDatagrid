<?php
namespace ZfcDatagrid\Column\Action;

class Button extends AbstractAction
{
    protected $label = '';

    public function __construct()
    {
        parent::__construct();

        $this->addClass('btn');
    }

    /**
     *
     * @param string $name
     */
    public function setLabel($name)
    {
        $this->label = (string) $name;
    }

    /**
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *
     * @return string
     */
    protected function getHtmlType()
    {
        if ($this->getLabel() == '') {
            throw new \InvalidArgumentException('A label is required for this action type, please call $action->setLabel()!');
        }

        return $this->getLabel();
    }
}
