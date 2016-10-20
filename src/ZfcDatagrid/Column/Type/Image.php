<?php
/**
 * Image type.
 */
namespace ZfcDatagrid\Column\Type;

use InvalidArgumentException;

class Image extends AbstractType
{
    /**
     * @var string
     */
    protected $resizeType = 'fixed';

    /**
     * @var float
     */
    protected $resizeHeight = 20.5;

    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'image';
    }

    /**
     * Set the resize type for TCPDF export.
     *
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    public function setResizeType($type)
    {
        if ($type != 'fixed' && $type != 'dynamic') {
            throw new InvalidArgumentException('Only dynamic or fixed is allowed as Type');
        }

        $this->resizeType = $type;
    }

    /**
     * @return string
     */
    public function getResizeType()
    {
        return $this->resizeType;
    }

    /**
     * @param float $height
     */
    public function setResizeHeight($height)
    {
        $this->resizeHeight = $height;
    }

    /**
     * @return float
     */
    public function getResizeHeight()
    {
        return $this->resizeHeight;
    }
}
