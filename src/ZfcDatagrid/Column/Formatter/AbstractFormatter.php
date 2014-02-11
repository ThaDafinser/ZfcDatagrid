<?php
namespace ZfcDatagrid\Column\Formatter;

use ZfcDatagrid\Column\AbstractColumn;

abstract class AbstractFormatter
{

    private $data = array();

    private $rendererName;

    protected $validRenderers = array();

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

    /**
     *
     * @param string $name            
     */
    public function setRendererName($name = null)
    {
        $this->rendererName = $name;
    }

    /**
     *
     * @return string null
     */
    public function getRendererName()
    {
        return $this->rendererName;
    }

    /**
     *
     * @param array $validRendrerers            
     */
    public function setValidRendererNames(array $validRendrerers)
    {
        $this->validRenderers = $validRendrerers;
    }

    /**
     *
     * @return array
     */
    public function getValidRendererNames()
    {
        return $this->validRenderers;
    }

    /**
     *
     * @return boolean
     */
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
            return $this->getFormattedValue($column);
        }
        
        return $data[$column->getUniqueId()];
    }

    /**
     *
     * @param AbstractColumn $columnUniqueId            
     *
     * @return string
     */
    abstract public function getFormattedValue(AbstractColumn $column);
}
