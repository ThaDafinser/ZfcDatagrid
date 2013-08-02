<?php
namespace ZfcDatagrid\Column\Action;

class Icon extends AbstractAction
{

    protected $iconClass;

    protected $iconLink;

    /**
     * Set the icon class (CSS)
     * - used for HTML if provided, overwise the iconLink is used
     *
     * @param string $name            
     */
    public function setIconClass($name)
    {
        $this->iconClass = (string) $name;
    }

    public function getIconClass()
    {
        return $this->iconClass;
    }

    public function hasIconClass()
    {
        if ($this->getIconClass() != '') {
            return true;
        }
        
        return false;
    }

    /**
     * Set the icon link (used for export, or also HTML, if no icon class is provided)
     *
     * @param string $http            
     */
    public function setIconLink($http)
    {
        $this->iconLink = (string) $http;
    }

    /**
     * Get the icon link
     *
     * @return string
     */
    public function getIconLink()
    {
        return $this->iconLink;
    }

    public function hasIconLink()
    {
        if ($this->getIconLink() != '') {
            return true;
        }
        
        return false;
    }

    public function toHtml()
    {
        if ($this->hasIconClass() === true) {
            // a css class is provided, so use it
            $this->addClass($this->getIconClass());
            
            $attributes = array();
            foreach ($this->getAttributes() as $attrKey => $attrValue) {
                $attributes[] = $attrKey . '="' . $attrValue . '"';
            }
            
            $attributes = implode(' ', $attributes);
            
            return '<i title="' . $this->getTitle() . '" ' . $attributes . '></i>';
        } elseif ($this->hasIconLink() === true) {
            // no css class -> use the icon link instead
            return '<img src="' . $this->getIconLink() . '" />';
        } else {
            throw new \InvalidArgumentException('Either a link or a class for the icon is required');
        }
    }
}