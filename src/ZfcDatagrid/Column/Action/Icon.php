<?php
namespace ZfcDatagrid\Column\Action;

class Icon extends AbstractAction
{

    protected $iconClass;

    protected $iconLink;

    public function setIconClass ($name)
    {
        $this->iconClass = (string) $name;
    }

    public function setIconLink ($http)
    {
        $this->iconLink = (string) $http;
    }

    public function toHtml ()
    {
        return '<i class="' . $this->iconClass . '" title="' . $this->getTitle() . '"></i>';
    }
}