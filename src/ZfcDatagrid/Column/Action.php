<?php
namespace ZfcDatagrid\Column;

class Action extends AbstractColumn
{

    private $actions = array();

    public function __construct ($uniqueId = 'action')
    {
        $this->setUniqueId($uniqueId);
        $this->setLabel('actions');
        
        $this->setUserSortDisabled(true);
        $this->setUserFilterDisabled(true);
    }

    public function addAction (Action\AbstractAction $action)
    {
        $this->actions[] = $action;
    }

    /**
     *
     * @return array
     */
    public function getActions ()
    {
        return $this->actions;
    }
}
