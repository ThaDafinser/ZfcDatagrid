<?php
namespace ZfcDatagrid\Column\Style;

class Align extends AbstractStyle
{
    /**
     *
     * @var string
     */
    const $LEFT = 'left';

    /**
     *
     * @var string
     */
    const $RIGHT = 'right';

    /**
     *
     * @var string
     */
    const $CENTER = 'center';

    /**
     *
     * @var string
     */
    const $JUSTIFY = 'justify';

    /**
     *
     * @var string
     */
    protected $alignment;

    public function __construct($alignment = self::LEFT)
    {
        $this->setAlignment($alignment);
    }

    /**
     *
     * @param string $alignment
     */
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    /**
     *
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }
}
