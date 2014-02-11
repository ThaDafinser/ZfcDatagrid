<?php
namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

class Image extends AbstractFormatter
{

    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
        'printHtml'
    );

    protected $attributes = array();

    protected $linkAttributes = array();
    
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function setLinkAttribute($name, $value)
    {
        $this->linkAttributes[$name] = $value;
    }

    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
        $value = $row[$column->getUniqueId()];
        
        
        if (is_array($value)) {
            $thumb = $value[0];
            
            if (isset($value[1])) {
                $original = $value[1];
            } else {
                $original = $thumb;
            }
        } else {
            $thumb = $value;
            $original = $value;
        }
        
        $linkAttributes = array();
        foreach ($this->getLinkAttributes() as $key => $value) {
            $linkAttributes[] = $key . '="' . $value . '"';
        }
        
        $attributes = array();
        foreach ($this->getAttributes() as $key => $value) {
            $attributes[] = $key . '="' . $value . '"';
        }
        
        return '<a href="' . $original . '" ' . implode(' ', $linkAttributes) . '><img src="' . $thumb . '" ' . implode(' ', $attributes) . ' /></a>';
    }
}
