<?php
namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

class Link extends AbstractFormatter
{

    protected $validRenderers = array(
        'jqgrid',
        'bootstrapTable'
    );

    public function getFormattedValue($value, $columnUniqueId)
    {
        return '<a href="' . $value . '">' . $value . '</a>';
    }
}
