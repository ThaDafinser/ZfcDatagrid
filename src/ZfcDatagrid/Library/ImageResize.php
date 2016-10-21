<?php

namespace ZfcDatagrid\Library;

class ImageResize
{
    /**
     * Calculate the width / height, respecting the ratio.
     *
     * @param float $width
     * @param float $height
     * @param float $maxWidth
     * @param float $maxHeight
     *
     * @return array
     */
    public static function getCalculatedSize($width, $height, $maxWidth, $maxHeight)
    {
        $widthDiffRatio = $maxWidth / $width;
        $heightDiffRatio = $maxHeight / $height;

        if ($widthDiffRatio <= $heightDiffRatio) {
            // resize based on width
            $newWidth = $maxWidth;
            $newHeight = $height * $widthDiffRatio;
        } else {
            // resize based on height
            $newWidth = $width * $heightDiffRatio;
            $newHeight = $maxHeight;
        }

        return [
            $newWidth,
            $newHeight,
        ];
    }
}
