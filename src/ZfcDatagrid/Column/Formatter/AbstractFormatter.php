<?php
namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

abstract class AbstractFormatter
{

    private $columns = array();

    private $data = array();

    private $rendererName;

    protected $validRenderers = array();

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    public function setRowData(array $data)
    {
        $this->data = $data;
    }

    /**
     *
     * @return array
     */
    public function getRowData()
    {
        return $this->data;
    }

    public function setRenderer($name = null)
    {
        $this->rendererName = $name;
    }

    public function getRendererName()
    {
        return $this->rendererName;
    }

    public function isApply()
    {
        if (in_array($this->getRendererName(), $this->validRenderers)) {
            return true;
        }
        
        return false;
    }

    /**
     *
     * @param AbstractColumn $column            
     * @return string
     */
    public function format(AbstractColumn $column)
    {
        $data = $this->getRowData();
        if ($this->isApply() === true) {
            return $this->getFormattedValue($data[$column->getUniqueId()], $column->getUniqueId());
        }
        
        return $data[$column->getUniqueId()];
    }

    /**
     *
     * @param string $value            
     * @param string $columnUniqueId            
     *
     * @return string
     */
    abstract public function getFormattedValue($value, $columnUniqueId);
}
