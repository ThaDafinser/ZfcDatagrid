<?php

namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

class Image extends AbstractFormatter
{
    protected $validRenderers = [
        'jqGrid',
        'bootstrapTable',
        'printHtml',
    ];

    protected $attributes = [];

    protected $prefix;

    protected $linkAttributes = [];

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

    /**
     * Get the prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the prefix of the image path and the prefix of the link.
     *
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
        $value = $row[$column->getUniqueId()];
        $prefix = $this->getPrefix();

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

        $linkAttributes = [];
        foreach ($this->getLinkAttributes() as $key => $value) {
            $linkAttributes[] = $key.'="'.$value.'"';
        }

        $attributes = [];
        foreach ($this->getAttributes() as $key => $value) {
            $attributes[] = $key.'="'.$value.'"';
        }

        return '<a href="'.$prefix.$original.'" '.implode(' ', $linkAttributes).'><img src="'.$prefix.$thumb.'" '.implode(' ', $attributes).' /></a>';
    }
}
