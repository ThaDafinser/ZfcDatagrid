<?php

namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

class Email extends AbstractFormatter
{
    /** @var array */
    protected $validRenderers = [
        'jqGrid',
        'bootstrapTable',
    ];

    /**
     * @param AbstractColumn $column
     *
     * @return string
     */
    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();

        return '<a href="mailto:'.$row[$column->getUniqueId()].'">'.$row[$column->getUniqueId()].'</a>';
    }
}
