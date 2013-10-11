<?php
namespace ZfcDatagrid\Column;

use ZfcDatagrid\Filter;

abstract class AbstractColumn
{

    protected $label;

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

    public function setLabel ($name)
    {
        $this->label = (string) $name;
    }

    /**
     * Get the label
     *
     * @return string null
     */
    public function getLabel ()
    {
        return $this->label;
    }

    public function setUniqueId ($id)
    {
        $this->uniqueId = $id;
    }

    public function getUniqueId ()
    {
        return $this->uniqueId;
    }

    public function setSelect ($part1, $part2 = null)
    {
        $this->selectPart1 = $part1;
        $this->selectPart2 = $part2;
    }

    public function getSelectPart1 ()
    {
        return $this->selectPart1;
    }

    public function getSelectPart2 ()
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
    public function setWidth ($percent)
    {
        $this->width = (float) $percent;
    }

    /**
     * Get the width
     * @return number
     */
    public function getWidth ()
    {
        return $this->width;
    }

    public function setHidden ($mode = true)
    {
        $this->isHidden = (bool) $mode;
    }

    public function isHidden ()
    {
        return (bool) $this->isHidden;
    }

    public function setIdentity ($mode = true)
    {
        $this->isIdentity = (bool) $mode;
        
        // Because IDs are normally hidden
        $this->setHidden($mode);
    }

    public function isIdentity ()
    {
        return (bool) $this->isIdentity;
    }

    /**
     * Set the column type
     *
     * @param Type\AbstractType $type            
     */
    public function setType (Type\AbstractType $type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return Type\AbstractType
     */
    public function getType ()
    {
        if ($this->type === null) {
            $this->type = new Type\String();
        }
        
        return $this->type;
    }

    public function addStyle (Style\AbstractStyle $style)
    {
        $this->styles[] = $style;
    }

    public function getStyles ()
    {
        return $this->styles;
    }

    public function hasStyles ()
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
    public function setUserSortDisabled ($mode = true)
    {
        $this->userSortEnabled = (bool) ! $mode;
    }

    /**
     * Is user sort enabled?
     *
     * @return boolean
     */
    public function isUserSortEnabled ()
    {
        return (bool) $this->userSortEnabled;
    }

    /**
     * The data will get sorted by this column (by default)
     * If will be changed by the user per request (POST,GET .
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     * ..)
     *
     * @param integer $priority            
     * @param string $direction            
     */
    public function setSortDefault ($priority = 1, $direction = 'ASC')
    {
        $this->sortDefault = array(
            'priority' => $priority,
            'sortDirection' => $direction
        );
    }

    /**
     * Get the sort defaults
     *
     * @return array
     */
    public function getSortDefault ()
    {
        return $this->sortDefault;
    }

    /**
     * Does this column has sort defaults?
     *
     * @return boolean
     */
    public function hasSortDefault ()
    {
        if (count($this->sortDefault) > 0) {
            return true;
        }
        
        return false;
    }

    public function setSortActive ($direction = 'ASC')
    {
        $this->sortActive = $direction;
    }

    public function isSortActive ()
    {
        if ($this->sortActive !== null) {
            return true;
        }
        
        return false;
    }

    public function getSortActiveDirection ()
    {
        return $this->sortActive;
    }

    /**
     *
     * @param boolean $mode            
     */
    public function setUserFilterDisabled ($mode = true)
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
    public function setFilterDefaultValue ($value = null)
    {
        if ($value != '') {
            $this->filterDefaultValue = (string) $value;
        }
    }

    public function getFilterDefaultValue ()
    {
        return $this->filterDefaultValue;
    }

    public function hasFilterDefaultValue ()
    {
        if ($this->filterDefaultValue != '') {
            return true;
        } else {
            return false;
        }
    }

    public function setFilterDefaultOperation ($operation = Filter::LIKE)
    {
        $this->filterDefaultOperation = $operation;
    }

    public function getFilterDefaultOperation ()
    {
        if ($this->filterDefaultOperation != '') {
            return $this->filterDefaultOperation;
        }
        
        return $this->getType()->getFilterDefaultOperation();
    }

    public function setFilterSelectOptions (array $options = null, $noSelect = true)
    {
        if ($noSelect === true) {
            $nothing = array(
                '' => '-'
            );
            $options = array_merge($nothing, $options);
        }
        $this->filterSelectOptions = $options;
    }

    public function getFilterSelectOptions ()
    {
        return $this->filterSelectOptions;
    }

    public function hasFilterSelectOptions ()
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
    public function setFilterActive ($value = '')
    {
        $this->filterActive = (bool) true;
        $this->filterActiveValue = $value;
    }

    /**
     *
     * @return boolean
     */
    public function isFilterActive ()
    {
        return $this->filterActive;
    }

    public function getFilterActiveValue ()
    {
        return $this->filterActiveValue;
    }

    /**
     *
     * @return boolean
     */
    public function isUserFilterEnabled ()
    {
        return (bool) $this->userFilterEnabled;
    }

    /**
     * Enable data translation
     *
     * @param boolean $mode            
     */
    public function setTranslationEnabled ($mode = true)
    {
        $this->translationEnabled = (bool) $mode;
    }

    /**
     * Is data translation enabled?
     *
     * @return boolean
     */
    public function isTranslationEnabled ()
    {
        return (bool) $this->translationEnabled;
    }

    /**
     * Replace the column values with the applied values
     * 
     * @param array $values
     * @param boolean $notReplacedGetEmpty
     */
    public function setReplaceValues (array $values, $notReplacedGetEmpty = true)
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
    public function hasReplaceValues ()
    {
        if (count($this->replaceValues) > 0)
            return true;
        
        return false;
    }

    /**
     * 
     * @return array
     */
    public function getReplaceValues ()
    {
        return $this->replaceValues;
    }

    /**
     * 
     * @return boolean
     */
    public function notReplacedGetEmpty ()
    {
        return $this->notReplacedGetEmpty;
    }

    public function setRendererParameter ($name, $value, $rendererType = 'jqgrid')
    {
        if (! isset($this->rendererParameter[$rendererType])) {
            $this->rendererParameter[$rendererType] = array();
        }
        
        $parameters = $this->rendererParameter[$rendererType];
        $parameters[$name] = $value;
        
        $this->rendererParameter[$rendererType] = $parameters;
    }

    public function getRendererParameters ($rendererType = 'jqgrid')
    {
        if (! isset($this->rendererParameter[$rendererType])) {
            $this->rendererParameter[$rendererType] = array();
        }
        
        return $this->rendererParameter[$rendererType];
    }

    public function setRowClickDisabled ($mode = true)
    {
        $this->rowClickEnabled = (bool) ! $mode;
    }

    public function isRowClickEnabled ()
    {
        return $this->rowClickEnabled;
    }
}
