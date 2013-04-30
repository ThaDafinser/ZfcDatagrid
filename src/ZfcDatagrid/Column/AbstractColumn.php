<?php
namespace ZfcDatagrid\Column;

use ZfcDatagrid\Column\DataPopulation\Object;

abstract class AbstractColumn implements ColumnInterface
{

    protected $label = '';

    protected $uniqueId;

    protected $selectPart1;

    protected $selectPart2 = null;

    /**
     *
     * @var Type\TypeInterface
     */
    protected $type = null;

    protected $styles = array();

    protected $width = 5;

    protected $isHidden = false;

    protected $isIdentity = false;

    protected $userSortEnabled = true;

    protected $sortDefaults = array();

    protected $sortActive = null;

    protected $translationEnabled = false;

    protected $replaceValues = array();

    protected $notReplacedGetEmpty = true;

    /**
     *
     * @var DataPopulation\DataPopulationInterface
     */
    protected $dataPopulation;

    public function setLabel ($name)
    {
        $this->label = (string) $name;
    }

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

    public function setWidth ($percent)
    {
        $this->width = (int) $percent;
    }

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
     * @param Type\TypeInterface $type            
     */
    public function setType (Type\TypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return null Type\TypeInterface
     */
    public function getType ()
    {
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
     * ..)
     *
     * @param integer $priority            
     * @param string $direction            
     */
    public function setSortDefault ($priority = 1, $direction = 'ASC')
    {
        $this->sortDefaults = array(
            'priority' => $priority,
            'sortDirection' => $direction
        );
    }

    /**
     * Get the sort defaults
     *
     * @return array
     */
    public function getSortDefaults ()
    {
        return $this->sortDefaults;
    }

    /**
     * Does this column has sort defaults?
     *
     * @return boolean
     */
    public function hasSortDefaults ()
    {
        if (count($this->sortDefaults) > 0) {
            return true;
        }
        
        return false;
    }

    public function setSortActive ($mode = true, $direction = 'ASC')
    {
        if ($mode === true) {
            $this->sortActive = $direction;
        } else {
            $this->sortActive = null;
        }
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

    public function setReplaceValues (array $values, $notReplacedGetEmpty = true)
    {
        $this->replaceValues = $values;
        $this->notReplacedGetEmpty = (bool) $notReplacedGetEmpty;
    }

    public function hasReplaceValues ()
    {
        if (count($this->replaceValues) > 0)
            return true;
        
        return false;
    }

    public function getReplaceValues ()
    {
        return $this->replaceValues;
    }

    public function notReplacedGetEmpty ()
    {
        return $this->notReplacedGetEmpty;
    }

    /**
     *
     * @param DataPopulation\DataPopulationInterface $dataPopulation            
     */
    public function setDataPopulation (DataPopulation\DataPopulationInterface $dataPopulation)
    {
        if($dataPopulation instanceof DataPopulation\Object && $dataPopulation->getObject() === null){
            throw new \Exception('object is missing in DataPopulation\Object!');
        }
        
        $this->dataPopulation = $dataPopulation;
    }

    /**
     *
     * @return DataPopulation\DataPopulationInterface
     */
    public function getDataPopulation ()
    {
        return $this->dataPopulation;
    }

    public function hasDataPopulation ()
    {
        if ($this->dataPopulation !== null) {
            return true;
        }
        
        return false;
    }
}
