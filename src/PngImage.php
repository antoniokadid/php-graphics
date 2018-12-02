<?php

namespace Graphics;

/**
 * Class PngImage
 * 
 * @package Graphics
 */
class PngImage extends Image
{
    /**
     * PngImage constructor.
     *
     * @param int $width
     * @param int $height
     * @param null $resource
     *
     * @throws GraphicsException
     */
    public function __construct(int $width, int $height, $resource = NULL)
    {
        $this->init($width, $height, IMAGETYPE_JPEG, $resource);
    }

    /**
     * @inheritdoc
     */
    public function output(int $quality = 85): void
    {
        if (ob_get_length() !== FALSE)
            ob_clean();

        header_remove('Content-Type');
        header('Content-Type: image/png');

        imagepng($this->resource, NULL, $quality);
    }
}