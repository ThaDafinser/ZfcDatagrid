<?php
namespace ZfcDatagrid\Column;

use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Formatter\AbstractFormatter;

abstract class AbstractColumn
{
    protected $label = '';

    protected $uniqueId;

    protected $selectPart1;

    protected $selectPart2 = null;

    /**
     *
     * @var Type\AbstractType
     */
    protected $type = null;

    protected $styles = array();

    protected $width = 5;

    protected $isHidden = false;

    protected $isIdentity = false;

    protected $userSortEnabled = true;

    protected $sortDefault = array();

    protected $sortActive = null;

    protected $filterDefaultValue = null;

    protected $filterDefaultOperation = null;

    /**
     *
     * @var null array
     */
    protected $filterSelectOptions;

    protected $filterActive = null;

    protected $filterActiveValue = '';

    protected $userFilterEnabled = true;

    protected $translationEnabled = false;

    protected $replaceValues = array();

    protected $notReplacedGetEmpty = true;

    protected $rowClickEnabled = true;

    protected $rendererParameter = array();

    protected $formatter;

    public function setLabel($name)
    {
        $this->label = (string) $name;
    }

    /**
     * Get the label
     *
     * @return string null
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function setUniqueId($id)
    {
        $this->uniqueId = $id;
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     *
     * @todo Move this to Column\Select
     * @deprecated remove this
     */
    public function setSelect($part1, $part2 = null)
    {
        $this->selectPart1 = $part1;
        $this->selectPart2 = $part2;
    }

    /**
     *
     * @todo Move this to Column\Select
     * @deprecated remove this
     */
    public function getSelectPart1()
    {
        return $this->selectPart1;
    }

    /**
     *
     * @todo Move this to Column\Select
     * @deprecated remove this
     */
    public function getSelectPart2()
    {
        return $this->selectPart2;
    }

    /**
     * Set the width in "percent"
     * It will be calculated to 100% dependend on what is displayed
     * If it's a different output mode like Excel it's dependend on the papersize/orientation
     *
     * @param number $percent
     */
    public function setWidth($percent)
    {
        $this->width = (float) $percent;
    }

    /**
     * Get the width
     *
     * @return number
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Hide or show the column
     *
     * @param boolean $mode
     */
    public function setHidden($mode = true)
    {
        $this->isHidden = (bool) $mode;
    }

    /**
     * Is this column hidden?
     *
     * @return boolean
     */
    public function isHidden()
    {
        return (bool) $this->isHidden;
    }

    /**
     * Set this column as primaryKey column
     *
     * @param boolean $mode
     */
    public function setIdentity($mode = true)
    {
        $this->isIdentity = (bool) $mode;

        // Because IDs are normally hidden
        $this->setHidden($mode);
    }

    /**
     * Is this a primaryKey column?
     *
     * @return boolean
     */
    public function isIdentity()
    {
        return (bool) $this->isIdentity;
    }

    /**
     * Set the column type
     *
     * @param Type\AbstractType $type
     */
    public function setType(Type\AbstractType $type)
    {
        if ($type instanceof Type\Image && $this->hasFormatter() === false) {
            $this->setFormatter(new Formatter\Image());
            $this->setRowClickDisabled(true);
        }

        $this->type = $type;
    }

    /**
     *
     * @return Type\AbstractType
     */
    public function getType()
    {
        if (null === $this->type) {
            $this->type = new Type\String();
        }

        return $this->type;
    }

    /**
     * Set styles
     *
     * @param array $styles
     */
    public function setStyles(array $styles)
    {
        $this->styles = array();

        foreach ($styles as $style) {
            $this->addStyle($style);
        }
    }

    /**
     *
     * @param Style\AbstractStyle $style
     */
    public function addStyle(Style\AbstractStyle $style)
    {
        $this->styles[] = $style;
    }

    /**
     *
     * @return Style\AbstractStyle[]
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     *
     * @return boolean
     */
    public function hasStyles()
    {
        if (count($this->styles) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Is the user allowed to do sort on this column?
     *
     * @param boolean $mode
     */
    public function setUserSortDisabled($mode = true)
    {
        $this->userSortEnabled = (bool) ! $mode;
    }

    /**
     * Is user sort enabled?
     *
     * @return boolean
     */
    public function isUserSortEnabled()
    {
        return (bool) $this->userSortEnabled;
    }

    /**
     * The data will get sorted by this column (by default)
     * If will be changed by the user per request (POST,GET....)
     *
     * @param integer $priority
     * @param string  $direction
     */
    public function setSortDefault($priority = 1, $direction = 'ASC')
    {
        $this->sortDefault = array(
            'priority' => $priority,
            'sortDirection' => $direction,
        );
    }

    /**
     * Get the sort defaults
     *
     * @return array
     */
    public function getSortDefault()
    {
        return $this->sortDefault;
    }

    /**
     * Does this column has sort defaults?
     *
     * @return boolean
     */
    public function hasSortDefault()
    {
        if (count($this->sortDefault) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Set that the data is getting sorted by this columns
     *
     * @param string $direction
     */
    public function setSortActive($direction = 'ASC')
    {
        $this->sortActive = $direction;
    }

    /**
     *
     * @return boolean
     */
    public function isSortActive()
    {
        if ($this->sortActive !== null) {
            return true;
        }

        return false;
    }

    /**
     *
     * @return string
     */
    public function getSortActiveDirection()
    {
        return $this->sortActive;
    }

    /**
     *
     * @param boolean $mode
     */
    public function setUserFilterDisabled($mode = true)
    {
        $this->userFilterEnabled = (bool) ! $mode;
    }

    /**
     * Set the default filterung value (used as long no user filtering getting applied)
     * Examples
     * $grid->setFilterDefaultValue('something');
     * $grid->setFilterDefaultValue('>20');
     *
     * OPERATORS are ALLOWED (like for the user)
     *
     * @param string $value
     */
    public function setFilterDefaultValue($value = null)
    {
        if ($value != '') {
            $this->filterDefaultValue = (string) $value;
        }
    }

    /**
     *
     * @return string
     */
    public function getFilterDefaultValue()
    {
        return $this->filterDefaultValue;
    }

    /**
     *
     * @return boolean
     */
    public function hasFilterDefaultValue()
    {
        if ($this->filterDefaultValue != '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param string $operation
     */
    public function setFilterDefaultOperation($operation = Filter::LIKE)
    {
        $this->filterDefaultOperation = $operation;
    }

    /**
     *
     * @return string
     */
    public function getFilterDefaultOperation()
    {
        if ($this->filterDefaultOperation != '') {
            return $this->filterDefaultOperation;
        }

        return $this->getType()->getFilterDefaultOperation();
    }

    /**
     *
     * @param array   $options
     * @param boolean $noSelect
     */
    public function setFilterSelectOptions(array $options = null, $noSelect = true)
    {
        // This work also with options with integer based array index such as
        // array(0 => 'zero', 1 => 'once', 2 => 'double', 3 => 'triple'....)

        if (true === $noSelect) {
            $options = array('' => '-') + $options;
            $this->setFilterDefaultValue('');
        }

        $this->filterSelectOptions = $options;
    }

    /**
     * Unset the filter select options (normal search)
     */
    public function unsetFilterSelectOptions()
    {
        $this->filterSelectOptions = null;
    }

    /**
     *
     * @return array null
     */
    public function getFilterSelectOptions()
    {
        return $this->filterSelectOptions;
    }

    /**
     *
     * @return boolean
     */
    public function hasFilterSelectOptions()
    {
        if (is_array($this->filterSelectOptions)) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param boolean $mode
     */
    public function setFilterActive($value = '')
    {
        $this->filterActive = (bool) true;
        $this->filterActiveValue = $value;
    }

    /**
     *
     * @return boolean
     */
    public function isFilterActive()
    {
        return $this->filterActive;
    }

    /**
     *
     * @return string
     */
    public function getFilterActiveValue()
    {
        return $this->filterActiveValue;
    }

    /**
     *
     * @return boolean
     */
    public function isUserFilterEnabled()
    {
        return (bool) $this->userFilterEnabled;
    }

    /**
     * Enable data translation
     *
     * @param boolean $mode
     */
    public function setTranslationEnabled($mode = true)
    {
        $this->translationEnabled = (bool) $mode;
    }

    /**
     * Is data translation enabled?
     *
     * @return boolean
     */
    public function isTranslationEnabled()
    {
        return (bool) $this->translationEnabled;
    }

    /**
     * Replace the column values with the applied values
     *
     * @param array   $values
     * @param boolean $notReplacedGetEmpty
     */
    public function setReplaceValues(array $values, $notReplacedGetEmpty = true)
    {
        $this->replaceValues = $values;
        $this->notReplacedGetEmpty = (bool) $notReplacedGetEmpty;

        $this->setFilterDefaultOperation(Filter::EQUAL);
        $this->setFilterSelectOptions($values);
    }

    /**
     *
     * @return boolean
     */
    public function hasReplaceValues()
    {
        if (count($this->replaceValues) > 0) {
            return true;
        }

        return false;
    }

    /**
     *
     * @return array
     */
    public function getReplaceValues()
    {
        return $this->replaceValues;
    }

    /**
     *
     * @return boolean
     */
    public function notReplacedGetEmpty()
    {
        return $this->notReplacedGetEmpty;
    }

    /**
     * Set parameter for a specific renderer (currently only supported for jqGrid)
     *
     * @param string $name
     * @param mixed  $value
     * @param string $rendererType
     */
    public function setRendererParameter($name, $value, $rendererType = 'jqGrid')
    {
        if (! isset($this->rendererParameter[$rendererType])) {
            $this->rendererParameter[$rendererType] = array();
        }

        $parameters = $this->rendererParameter[$rendererType];
        $parameters[$name] = $value;

        $this->rendererParameter[$rendererType] = $parameters;
    }

    /**
     *
     * @param  string $rendererType
     * @return array
     */
    public function getRendererParameters($rendererName = 'jqGrid')
    {
        if (! isset($this->rendererParameter[$rendererName])) {
            $this->rendererParameter[$rendererName] = array();
        }

        return $this->rendererParameter[$rendererName];
    }

    /**
     * Set a a template formatter
     *
     * @param AbstractFormatter $formatter
     */
    public function setFormatter(AbstractFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     *
     * @param  string $rendererName
     * @return NULL   AbstractFormatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     *
     * @param  string  $rendererType
     * @return boolean
     */
    public function hasFormatter()
    {
        if ($this->formatter !== null) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param boolean $mode
     */
    public function setRowClickDisabled($mode = true)
    {
        $this->rowClickEnabled = (bool) ! $mode;
    }

    /**
     *
     * @return boolean
     */
    public function isRowClickEnabled()
    {
        return $this->rowClickEnabled;
    }
}
