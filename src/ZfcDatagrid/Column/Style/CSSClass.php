<?php
namespace ZfcDatagrid\Column\Style;

/**
 * Css class for the row/cell
 */
class CSSClass extends AbstractStyle
{
    private $class;
    private $forRow;

    /**
     * @param string|array $class
     * @param bool   $forRow
     */
    public function __construct($class, $forRow = false)
    {
        $this->class  = $class;
        $this->forRow = $forRow;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        if (is_array($this->class)) {
            return implode(' ', $this->class);
        }
        return $this->class;
    }

    /**
     * @param string|array $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }
}