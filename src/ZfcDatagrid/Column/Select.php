<?php
namespace ZfcDatagrid\Column;

class Select extends AbstractColumn
{
    /**
     * Specific column function wrapper LIKE MONTH(%s)
     *
     * @var string
     */
    private $colfunctionWrapper;

    /**
     * Possible calls:
     * $column = new Column('id', 'user')
     * Select the id from the user table -> UNIQUE is the comination of both
     * $column = new Column('title')
     * Just the title from an array -> UNIQUE will be just the first parameter
     * $column = new Column('(SELECT GROUP_CONCAT....)', 'someAlias')
     * Use the subselect -> UNIQUE will be the second parameter
     *
     * @param string|object $columnOrIndexOrObject            
     * @param string $tableOrAliasOrUniqueId            
     */
    public function __construct ($columnOrIndexOrObject, $tableOrAliasOrUniqueId = null)
    {
        if ($tableOrAliasOrUniqueId !== null && ! is_string($tableOrAliasOrUniqueId)) {
            throw new \Exception('Variable $tableOrAliasOrUniqueId must be null or a string');
        }
        
        if (is_string($columnOrIndexOrObject) && $tableOrAliasOrUniqueId !== null) {
            // $column = new Column('id', 'user')
            $this->setUniqueId($tableOrAliasOrUniqueId . '_' . $columnOrIndexOrObject);
            $this->setSelect($tableOrAliasOrUniqueId, $columnOrIndexOrObject);
        } elseif (is_string($columnOrIndexOrObject)) {
            // $column = new Column('title')
            $this->setUniqueId($columnOrIndexOrObject);
            $this->setSelect($columnOrIndexOrObject);
        } elseif (is_object($columnOrIndexOrObject) && $tableOrAliasOrUniqueId !== null && is_string($tableOrAliasOrUniqueId)) {
            // $column = new Column('(SELECT GROUP_CONCAT....)', 'someAlias')
            $this->setUniqueId($tableOrAliasOrUniqueId);
            $this->setSelect($columnOrIndexOrObject);
        } else {
            throw new \Exception('Column was not initiated correctly, please read the __construct docblock!');
        }
    }

    /**
     * @param string $colfunctionWrapper
     */
    public function setColfunctionWrapper($colfunctionWrapper)
    {
        $this->colfunctionWrapper = $colfunctionWrapper;
    }

    /**
     * @return string
     */
    public function getColfunctionWrapper()
    {
        return $this->colfunctionWrapper;
    }
}
