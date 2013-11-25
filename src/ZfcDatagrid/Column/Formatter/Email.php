<?php
namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

class Email extends AbstractFormatter
{

    protected $validRenderers = array(
        'jqgrid',
        'bootstrapTable'
    );

    public function getFormattedValue($value, $columnUniqueId)
    {
        return '<a href="mailto:' . $value . '">' . $value . '</a>';
    }
}
