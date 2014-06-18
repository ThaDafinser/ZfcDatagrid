<?php
namespace ZfcDatagrid\Action;

class Mass
{

    /**
     *
     * @var string
     */
    private $title = '';

    /**
     *
     * @var string
     */
    private $link = '';

    /**
     *
     * @var boolean
     */
    private $confirm = false;

    /**
     *
     * @param string  $title
     * @param string  $link
     * @param boolean $confirm
     */
    public function __construct($title = '', $link = '', $confirm = false)
    {
        $this->setTitle($title);
        $this->setLink($link);
        $this->setConfirm($confirm);
    }

    /**
     *
     * @param string $title
     * @return Mass
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $link
     * @return Mass
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     *
     * @param boolean $mode
     * @return Mass
     */
    public function setConfirm($mode = true)
    {
        $this->confirm = (bool) $mode;

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getConfirm()
    {
        return $this->confirm;
    }
}
