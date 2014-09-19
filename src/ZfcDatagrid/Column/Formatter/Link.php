<?php
namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

class Link extends AbstractFormatter
{
    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
    );

    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
        $value = $row[$column->getUniqueId()];

        return '<a href="'.$value.'">'.$value.'</a>';
    }
}
