<?php
namespace ZfcDatagrid\Column\Type;

/**
 * Try to render an image
 */
class Image extends AbstractType
{

    protected $width;

    protected $height;

    public function __construct($width = null, $height = null)
    {
        $this->setWidth($width);
        $this->setHeight($height);
    }

    public function getTypeName()
    {
        return 'image';
    }

    public function setWidth($width)
    {
        $this->width = (int) $width;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setHeight($height)
    {
        $this->height = (int) $height;
    }

    public function getHeight()
    {
        return $this->height;
    }
}
