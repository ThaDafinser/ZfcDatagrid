<?php
namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

class Email extends AbstractFormatter
{
    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
    );

    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();

        return '<a href="mailto:'.$row[$column->getUniqueId()].'">'.$row[$column->getUniqueId()].'</a>';
    }
}
