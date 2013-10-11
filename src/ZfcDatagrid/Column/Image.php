<?php
namespace ZfcDatagrid\Column;

/**
 * Display images
 */
class Image extends Standard
{
	/**
	 * @var int
	 */
	private $imageHeight;

	/**
	 * @var int
	 */
	private $imageWidth;

	/**
	 * @param mixed $height
	 *
	 * return Image
	 */
	public function setImageHeight($height)
	{
		$this->imageHeight = $height;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getImageHeight()
	{
		return $this->imageHeight;
	}

	/**
	 * @param mixed $width
	 *
	 * @return Image
	 */
	public function setImageWidth($width)
	{
		$this->imageWidth = $width;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getImageWidth()
	{
		return $this->imageWidth;
	}

	public function getImageStyleTag()
	{
		if (empty($this->imageWidth) && empty($this->imageHeight)) {
			return '';
		}
		if (empty($this->imageHeight)) {
			return ' style="width: ' . $this->imageWidth . 'px" ';
		}
		if (empty($this->imageWidth)) {
			return ' style="height: ' . $this->imageHeight . 'px" ';
		}
		return ' style="height: ' . $this->imageHeight . 'px; width: ' . $this->imageWidth . 'px" ';
	}

    public function __construct($columnOrIndexOrObject = 'image', $tableOrAliasOrUniqueId = null)
    {
        parent::__construct($columnOrIndexOrObject, $tableOrAliasOrUniqueId);
        
        $this->setUserSortDisabled(true);
        $this->setUserFilterDisabled(true);
    }
}
