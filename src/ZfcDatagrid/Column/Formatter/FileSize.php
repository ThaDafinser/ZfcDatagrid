<?php

namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

class FileSize extends AbstractFormatter
{
    /**
     * We implement isApply here ourself, because it's always valid!
     *
     * @var array
     */
    protected $validRenderers = [];

    /** @var array */
    protected static $prefixes = [
        '',
        'K',
        'M',
        'G',
        'T',
        'P',
        'E',
        'Z',
        'Y',
    ];

    /**
     * @return bool
     */
    public function isApply()
    {
        return true;
    }

    /**
     * The value should be in bytes.
     *
     * @see \ZfcDatagrid\Column\Formatter\AbstractFormatter::getFormattedValue()
     */
    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
        $value = $row[$column->getUniqueId()];

        if ('' == $value) {
            return $value;
        }

        $index = 0;
        while ($value >= 1024 && $index < count(self::$prefixes)) {
            $value = $value / 1024;
            ++$index;
        }

        return sprintf('%1.2f %sB', $value, self::$prefixes[$index]);
    }
}
