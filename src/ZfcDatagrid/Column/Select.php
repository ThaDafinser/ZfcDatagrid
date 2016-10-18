<?php

namespace ZfcDatagrid\Column;

class Select extends AbstractColumn
{
    /** @var string */
    protected $selectPart1;

    /** @var string|object|null */
    protected $selectPart2 = null;

    /**
     * Specific column function filter e.g.
     * WHERE MONTH(%s).
     *
     * @var string
     */
    private $filterSelectExpression;

    /**
     * Possible calls:
     * $column = new Column('id', 'user')
     * Select the id from the user table -> UNIQUE is the comination of both
     * $column = new Column('title')
     * Just the title from an array -> UNIQUE will be just the first parameter
     * $column = new Column('(SELECT GROUP_CONCAT....)', 'someAlias')
     * Use the subselect -> UNIQUE will be the second parameter.
     *
     * @param string|object $columnOrIndexOrObject
     * @param string        $tableOrAliasOrUniqueId
     *
     * @throws \Exception
     */
    public function __construct($columnOrIndexOrObject, $tableOrAliasOrUniqueId = null)
    {
        if ($tableOrAliasOrUniqueId !== null && !is_string($tableOrAliasOrUniqueId)) {
            throw new \Exception('Variable $tableOrAliasOrUniqueId must be null or a string');
        }

        if (is_string($columnOrIndexOrObject) && $tableOrAliasOrUniqueId !== null) {
            // $column = new Column('id', 'user')
            $this->setUniqueId($tableOrAliasOrUniqueId.'_'.$columnOrIndexOrObject);
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
     * @params string $part1
     * @params string|object|null $part2
     */
    public function setSelect($part1, $part2 = null)
    {
        $this->selectPart1 = $part1;
        $this->selectPart2 = $part2;
    }

    /**
     * @return string
     */
    public function getSelectPart1()
    {
        return $this->selectPart1;
    }

    /**
     * @return string|object|null
     */
    public function getSelectPart2()
    {
        return $this->selectPart2;
    }

    /**
     * @param string $filterSelectExpression
     */
    public function setFilterSelectExpression($filterSelectExpression)
    {
        $this->filterSelectExpression = $filterSelectExpression;
    }

    /**
     * @return string
     */
    public function getFilterSelectExpression()
    {
        return $this->filterSelectExpression;
    }

    /**
     * @return bool
     */
    public function hasFilterSelectExpression()
    {
        if ($this->filterSelectExpression !== null) {
            return true;
        }

        return false;
    }
}
