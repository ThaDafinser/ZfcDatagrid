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

    /**
     *
     * @param string $uniqueId
     */
    public function __construct($uniqueId = 'action')
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
     * @return self
     */
    public function addAction(Action\AbstractAction $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array|Action\AbstractAction[] $actions
     *
     * @return self
     */
    public function setActions(array $actions)
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
     * @return self
     */
    public function removeAction($key = null)
    {
        unset($this->actions[$key]);

        return $this;
    }

    /**
     * @return self
     */
    public function clearActions()
    {
        $this->actions = array();

        return $this;
    }
}
