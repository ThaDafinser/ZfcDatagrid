<?php
namespace ZfcDatagridTest\Library;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Library\ImageResize;

/**
 * @group Library
 * @covers \ZfcDatagrid\Library\ImageResize
 */
class ImageResizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Height + width is LESS than max
     *
     * Resize base on one of both
     */
    public function testLandscapeLessBothResizeBothPossible()
    {
        // "landscape"
        $width  = 10;
        $height = 5;

        $maxWidth  = 20;
        $maxHeight = 10;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(20, $newWidth);
        $this->assertEquals(10, $newHeight);
    }

    /**
     * Height + width is LESS than max
     *
     * Resize based on width
     */
    public function testLandscapeLessBothResizeOnWidth()
    {
        // "landscape"
        $width  = 10;
        $height = 5;

        $maxWidth  = 18;
        $maxHeight = 10;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(18, $newWidth);
        $this->assertEquals(9, $newHeight);
    }

    /**
     * Height + width is LESS than max
     *
     * Resize based on width
     */
    public function testLandscapeLessBothResizeOnHeight()
    {
        // "landscape"
        $width  = 10;
        $height = 5;

        $maxWidth  = 20;
        $maxHeight = 8;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(16, $newWidth);
        $this->assertEquals(8, $newHeight);
    }

    /**
     * Height + width is LESS than max
     *
     * Resize base on one of both
     */
    public function testPortraitLessBothResizeBothPossible()
    {
        // "landscape"
        $width  = 2.5;
        $height = 5;

        $maxWidth  = 20;
        $maxHeight = 10;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(5, $newWidth);
        $this->assertEquals(10, $newHeight);
    }

    /**
     * Height + width is LESS than max
     *
     * Resize based on width
     */
    public function testPortraitLessBothResizeOnWidth()
    {
        // "landscape"
        $width  = 10;
        $height = 4;

        $maxWidth  = 20;
        $maxHeight = 10;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(20, $newWidth);
        $this->assertEquals(8, $newHeight);
    }

    /**
     * Height + width is LESS than max
     *
     * Resize based on width
     */
    public function testPortraitLessBothResizeOnHeight()
    {
        // "landscape"
        $width  = 10;
        $height = 6;

        $maxWidth  = 30;
        $maxHeight = 12;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(20, $newWidth);
        $this->assertEquals(12, $newHeight);
    }

    /**
     * Height + width is LESS than max
     *
     * Resize base on one of both
     */
    public function testLandscapeGreaterBoth()
    {
        // "landscape"
        $width  = 30;
        $height = 20;

        $maxWidth  = 15;
        $maxHeight = 10;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(15, $newWidth);
        $this->assertEquals(10, $newHeight);
    }

    /**
     * Height + width is LESS than max
     *
     * Resize base on one of both
     */
    public function testLandscapeGreaterWidth()
    {
        // "landscape"
        $width  = 30;
        $height = 10;

        $maxWidth  = 15;
        $maxHeight = 10;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(15, $newWidth);
        $this->assertEquals(5, $newHeight);
    }

    /**
     * Height + width is LESS than max
     *
     * Resize base on one of both
     */
    public function testLandscapeGreaterHeight()
    {
        // "landscape"
        $width  = 15;
        $height = 30;

        $maxWidth  = 15;
        $maxHeight = 10;

        $resize                     = new ImageResize();
        list($newWidth, $newHeight) = $resize->getCalculatedSize($width, $height, $maxWidth, $maxHeight);

        $this->assertEquals(5, $newWidth);
        $this->assertEquals(10, $newHeight);
    }
}
