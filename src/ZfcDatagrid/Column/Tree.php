<?php
namespace ZfcDatagrid\Column;
/**
 * @deprecated DO NOT USE THIS!!!!
 */
class Tree extends Standard
{

    const LEVEL_DEFAULT_INDENTATION = 2; 
    
    const TYPE_ADJANCENCY_LIST = 'adjacencyList';

    private $levelIdentation = self::LEVEL_DEFAULT_INDENTATION;
    
    private $treeType = self::TYPE_ADJANCENCY_LIST;

    /**
     *
     * @var AbstractColumn
     */
    private $columnParentId;

    /**
     *
     * @var AbstractColumn
     */
    private $columnHasChildren;

    /**
     *
     * @var AbstractColumn
     */
    private $columnLevel;

    /**
     * @return string
     */
    public function getTreeType ()
    {
        return $this->treeType;
    }
    
    public function setLevelIndentation($spaceCount = self::LEVEL_DEFAULT_INDENTATION){
        $this->levelIdentation = (int)$spaceCount;
    }
    
    /**
     * 
     * @return integer
     */
    public function getLevelIndentation(){
        return $this->levelIdentation;
    }

    /**
     * Enable a tree based on these three columns
     *
     * @param AbstractColumn $parentId            
     * @param AbstractColumn $hasChildren            
     * @param AbstractColumn $level            
     */
    public function setAdjacencyList (AbstractColumn $colParentId, AbstractColumn $colHasChildren, AbstractColumn $colLevel)
    {
        $this->columnParentId = $colParentId;
        $this->columnHasChildren = $colHasChildren;
        $this->columnLevel = $colLevel;
    }

    /**
     *
     * @param AbstractColumn $column            
     */
    public function setColumnParentId (AbstractColumn $column)
    {
        $this->columnParentId = $column;
    }

    /**
     *
     * @return \ZfcDatagrid\Column\AbstractColumn
     */
    public function getColumnParentId ()
    {
        return $this->columnParentId;
    }

    /**
     *
     * @param AbstractColumn $column            
     */
    public function setColumnHasChildren (AbstractColumn $column)
    {
        $this->columnHasChildren = $column;
    }

    /**
     *
     * @return AbstractColumn
     */
    public function getColumnHasChildren ()
    {
        return $this->columnHasChildren;
    }

    /**
     *
     * @param AbstractColumn $column            
     */
    public function setColumnLevel (AbstractColumn $column)
    {
        $this->columnLevel = $column;
    }

    /**
     *
     * @return \ZfcDatagrid\Column\AbstractColumn
     */
    public function getColumnLevel ()
    {
        return $this->columnLevel;
    }
    
    public function setActionOpen(Action\AbstractAction $action){
        $this->actionOpen = $action;
    }
    
    public function getActionOpen(){
        return $this->actionOpen;
    }
    
    public function setActionClose(Action\AbstractAction $action){
        $this->actionClose = $action;
    }
    
    public function getActionClose(){
        return $this->actionClose;
    }
}
