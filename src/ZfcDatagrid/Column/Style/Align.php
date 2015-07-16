<?php
namespace ZfcDatagrid\Column\Style;

class Align extends AbstractStyle
{
    /**
     *
     * @var string
     */
    public static $LEFT = 'left';

    /**
     *
     * @var string
     */
    public static $RIGHT = 'right';

    /**
     *
     * @var string
     */
    public static $CENTER = 'center';

    /**
     *
     * @var string
     */
    public static $JUSTIFY = 'justify';

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
     * @param  string $alignment
     * @return self
     */
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;

        return $this;
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
