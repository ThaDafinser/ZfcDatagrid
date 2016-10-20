<?php

namespace ZfcDatagrid\Column;

/**
 * Action Column
 * IMPORTANT: Will only be shown on HTML renderer.
 *
 * So Attributes for HTML are valid...
 */
class Action extends AbstractColumn
{
    /** @var Action\AbstractAction[] */
    private $actions = [];

    /**
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
     */
    public function addAction(Action\AbstractAction $action)
    {
        $this->actions[] = $action;
    }

    /**
     * @return Action\AbstractAction[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array|Action\AbstractAction[] $actions
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param int $key
     *
     * @return Action\AbstractAction|null
     */
    public function getAction($key)
    {
        if (isset($this->actions[$key])) {
            return $this->actions[$key];
        }

        return;
    }

    /**
     * @param int $key
     */
    public function removeAction($key = null)
    {
        unset($this->actions[$key]);
    }

    /**
     *
     */
    public function clearActions()
    {
        $this->actions = [];
    }
}
