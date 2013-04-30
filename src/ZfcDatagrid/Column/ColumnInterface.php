<?php
namespace ZfcDatagrid\Column;

interface ColumnInterface
{

    /**
     * Set the label of the column
     *
     * @param string $name            
     */
    public function setLabel ($name);

    /**
     * Get the label
     *
     * @return string
     */
    public function getLabel ();

    /**
     * Overwrite the Unique ID
     *
     * @param string $id            
     */
    public function setUniqueId ($id);

    /**
     * Get the uniqueId
     *
     * @return string
     */
    public function getUniqueId ();

    /**
     * Set the width
     *
     * @param integer $percent            
     */
    public function setWidth ($percent);

    /**
     *
     * @return integer
     */
    public function getWidth ();

    public function setHidden ($mode = true);

    public function isHidden ();

    /**
     * Set this column as a identity column
     * 
     * @param boolean $mode
     */
    public function setIdentity ($mode = true);

    /**
     * Is this a identity column?
     */
    public function isIdentity ();

    /**
     * Is the user allowed to do sort on this column?
     * 
     * @param boolean $mode
     */
    public function setUserSortDisabled ($mode = true);
    
    /**
     * Is user sort enabled?
     * 
     * @return boolean
     */
    public function isUserSortEnabled ();
    
    /**
     * The data will get sorted by this column (by default)
     * If will be changed by the user per request (POST,GET ...)
     * 
     * @param integer $priority            
     * @param string $direction            
     */
    public function setSortDefault ($priority = 1, $direction = 'ASC');

    /**
     * Get the sort defaults
     * @return array
     */
    public function getSortDefaults ();

    /**
     * Does this column has sort defaults?
     * 
     * @return boolean
     */
    public function hasSortDefaults ();

    /**
     * Enable data translation
     * 
     * @param boolean $mode            
     */
    public function setTranslationEnabled ($mode = true);

    /**
     * Is data translation enabled?
     * @return boolean
     */
    public function isTranslationEnabled ();
}

