<?php

namespace Graphics;

/**
 * Class BitmapImage
 *
 * @package Graphics
 */
class BitmapImage extends Image
{
    /**
     * BitmapImage constructor.
     *
     * @param int $width
     * @param int $height
     * @param null $resource
     *
     * @throws GraphicsException
     */
    public function __construct(int $width, int $height, $resource = NULL)
    {
        $this->init($width, $height, IMAGETYPE_BMP, $resource);
    }

    /**
     * @inheritdoc
     */
    public function output(int $quality = 85): void
    {
        if (ob_get_length() !== FALSE)
            ob_clean();

        header_remove('Content-Type');
        header('Content-Type: image/jpeg');

        imagebmp($this->resource);
    }
}