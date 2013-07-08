<?php
namespace ZfcDatagrid\Column\Action;

class Icon extends AbstractAction
{

    protected $iconClass;

    protected $iconLink;

    public function setIconClass ($name)
    {
        $this->addClass($name);
        $this->iconClass = (string) $name;
    }

    public function setIconLink ($http)
    {
        $this->iconLink = (string) $http;
    }

    public function toHtml ()
    {
        $attributes = array();
        foreach ($this->getAttributes() as $attrKey => $attrValue) {
            if (is_array($attrValue)) {
                $attrValue = implode(' ', $attrValue);
            }
            $attributes[] = $attrKey . '="' . $attrValue . '"';
        }
        
        $attributes = implode(' ', $attributes);
        
        return '<i title="' . $this->getTitle() . '" ' . $attributes . '></i>';
    }
}