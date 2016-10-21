<?php

namespace ZfcDatagrid\Action;

class Mass
{
    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $link = '';

    /**
     * @var bool
     */
    private $confirm = false;

    /**
     * @param string $title
     * @param string $link
     * @param bool   $confirm
     */
    public function __construct($title = '', $link = '', $confirm = false)
    {
        $this->setTitle($title);
        $this->setLink($link);
        $this->setConfirm($confirm);
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param bool $mode
     */
    public function setConfirm($mode = true)
    {
        $this->confirm = (bool) $mode;
    }

    /**
     * @return bool
     */
    public function getConfirm()
    {
        return $this->confirm;
    }
}
