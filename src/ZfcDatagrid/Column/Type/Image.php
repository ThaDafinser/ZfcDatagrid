<?php
namespace ZfcDatagrid\Column\Type;

/**
 * Try to render an image
 */
class Image extends AbstractType
{

    protected $minHeight;

    protected $maxHeight;

    public function __construct($minHeight = null, $maxHeight = null)
    {
        $this->setMinHeight($minHeight);
        $this->setMaxHeight($maxHeight);
    }

    public function getTypeName()
    {
        return 'image';
    }

    public function setMinHeight($minHeight)
    {
        $this->minHeight = (int) $minHeight;
    }

    public function getMinHeight()
    {
        return $this->minHeight;
    }

    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = (int) $maxHeight;
    }

    public function getMaxHeight()
    {
        return $this->maxHeight;
    }
}
