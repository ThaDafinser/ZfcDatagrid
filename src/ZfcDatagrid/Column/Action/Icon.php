<?php
namespace ZfcDatagrid\Column\Action;

class Icon extends AbstractAction
{

    protected $iconLink;

    /**
     * Set the icon class (CSS)
     * - used for HTML if provided, overwise the iconLink is used
     *
     * @param string $name            
     */
    public function setIconClass ($name)
    {
        $this->addClass($name);
    }

    /**
     * Set the icon link (used for export, or also HTML, if no icon class is provided)
     *
     * @param string $http            
     */
    public function setIconLink ($http)
    {
        $this->iconLink = (string) $http;
    }

    /**
     * Get the icon link
     *
     * @return string
     */
    public function getIconLink ()
    {
        return $this->iconLink;
    }

    public function toHtml ()
    {
        $attributes = array();
        foreach ($this->getAttributes() as $attrKey => $attrValue) {
            $attributes[] = $attrKey . '="' . $attrValue . '"';
        }
        
        $attributes = implode(' ', $attributes);
        
        if ($this->getAttribute('class') != '') {
            // a css class is provided, so use it
            return '<i title="' . $this->getTitle() . '" ' . $attributes . '></i>';
        } elseif ($this->getIconLink() != '') {
            // no css class -> use the icon link instead
            return '<img src="' . $this->getIconLink() . '" />';
        } else {
            throw new \InvalidArgumentException('Either a link or a class for the icon is required');
        }
    }
}