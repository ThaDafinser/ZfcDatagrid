<?php
namespace ZfcDatagrid\Column;

/**
 * Action Column
 * IMPORTANT: Will only be shown on HTML renderer
 * 
 * So Attributes for HTML are valid...
 *
 */
class Action extends AbstractColumn
{

    private $actions = array();

    public function __construct ($uniqueId = 'action')
    {
        $this->setUniqueId($uniqueId);
        $this->setLabel('Actions');
        
        $this->setUserSortDisabled(true);
        $this->setUserFilterDisabled(true);
        
        $this->setRowClickDisabled(true);
    }

    /**
     * @param Action\AbstractAction $action
     *
     * @return $this
     */
    public function addAction (Action\AbstractAction $action)
    {
        $this->actions[] = $action;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getActions ()
    {
        return $this->actions;
    }

    /**
     * @param array|Action\AbstractAction[] $actions
     *
     * @return $this
     */
    public function setActions (array $actions)
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * @param int $key
     *
     * @return mixed
     */
    public function getAction($key)
    {
        if (isset($this->actions[$key])) {
            return $this->actions[$key];
        }
    }

    /**
     * @param int $key
     *
     * @return $this
     */
    public function removeAction($key = null)
    {
        if (null === $key) {
            return $this->setActions(array());
        }
        unset ($this->actions[$key]);

        return $this;
    }
}
