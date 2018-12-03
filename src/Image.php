<?php

namespace Graphics;

/**
 * Class Image
 *
 * @package Graphics
 */
abstract class Image
{
    /** @var int */
    protected $width;
    /** @var int */
    protected $height;
    /** @var int */
    protected $type;
    /** @var float */
    protected $ratio;
    /** @var resource */
    protected $resource;

    public function __destruct()
    {
        if ($this->resource != NULL && is_resource($this->resource))
            @imagedestroy($this->resource);
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getRatio(): float
    {
        return $this->ratio;
    }

    /**
     * @param string $filename
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public static function FromFile($filename): Image
    {
        if (!file_exists($filename))
            throw new GraphicsException(sprintf('%s does not exist.', $filename));

        $handle = fopen($filename, 'r');
        $data = fread($handle, filesize($filename));
        fclose($handle);

        return self::FromData($data);
    }

    /**
     * @param string $base64
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public static function FromBase64(string $base64): Image
    {
        $data = base64_decode($base64);
        if ($data === FALSE)
            throw new GraphicsException('Unable to read Base64 data.');

        return self::FromData($data);
    }

    /**
     * @param $data
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public static function FromData(string $data): Image
    {
        $imageResource = @imagecreatefromstring($data);
        if ($imageResource === FALSE)
            throw new GraphicsException('Unable to process image data.');

        $size = getimagesizefromstring($data, $info);
        if ($size === FALSE)
            throw new GraphicsException('Unable to process image data.');

        list($width, $height, $type, $attr) = $size;

        switch ($type) {
            case IMAGETYPE_BMP:
                return new BitmapImage($width, $height, $imageResource);
            case IMAGETYPE_JPEG:
                return new JpegImage($width, $height, $imageResource);
            default:
                throw new GraphicsException('Invalid image type.');
        }
    }

    /**
     * @param int $width
     *
     * @throws GraphicsException
     */
    private static function validateWidth(int $width): void
    {
        if ($width <= 0)
            throw new GraphicsException('Width cannot be less or equal to zero.');
    }

    /**
     * @param int $height
     *
     * @throws GraphicsException
     */
    private static function validateHeight(int $height): void
    {
        if ($height <= 0)
            throw new GraphicsException('Height cannot be less or equal to zero.');
    }

    /**
     * Create a temporary image and produce the top left corner.
     *
     * @param int $radius
     * @param bool $isTrueColor
     *
     * @return resource
     *
     * @throws GraphicsException
     */
    private static function generateCorner(int $radius, bool $isTrueColor)
    {
        $corner = $isTrueColor ?
            @imagecreatetruecolor($radius, $radius) :
            @imagecreate($radius, $radius);

        if ($corner === FALSE)
            throw new GraphicsException('Unable to create temporary image.');
        if (@imageantialias($corner, TRUE) === FALSE)
            throw new GraphicsException('Unable to activate anti alias.');
        if (@imagealphablending($corner, FALSE) === FALSE)
            throw new GraphicsException('Unable to de-activate alpha blending.');
        if (@imagesavealpha($corner, TRUE) === FALSE)
            throw new GraphicsException('Unable to activate save alpha.');

        $white = (new Color($corner, 255, 255, 255))->getId();
        $black = (new Color($corner, 0, 0, 0, 127))->getId();

        @imagecolortransparent($corner, $white);

        // TOP LEFT
        if (@imagefilledarc($corner, $radius, $radius, $radius * 2, $radius * 2, 180, 270, $white, IMG_ARC_PIE) === FALSE)
            throw new GraphicsException('Unable to draw arc.');
        if (@imagefilltoborder($corner, 0, 0, $white, $black) === FALSE)
            throw new GraphicsException('Unable to fill to border.');

        return $corner;
    }

    /**
     * Resize an image to the specified width and keep the ratio between width and height.
     *
     * @param int $width
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function resizeToWidth(int $width): Image
    {
        self::validateWidth($width);
        $this->resize($width, ceil($width / $this->ratio));

        return $this;
    }

    /**
     * Resize an image to the specified height and keep the ratio between width and height.
     *
     * @param int $height
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function resizeToHeight(int $height): Image
    {
        self::validateHeight($height);
        $this->resize(ceil($height * $this->ratio), $height);

        return $this;
    }

    /**
     * Resize an image to the specified width and height. Ratio is not preserved.
     *
     * @param int $width
     * @param int $height
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function resize(int $width, int $height): Image
    {
        self::validateWidth($width);
        $this->validateHeight($height);

        $destinationImage = @imageistruecolor($this->resource) ?
            @imagecreatetruecolor($width, $height) :
            @imagecreate($width, $height);

        if ($destinationImage === FALSE)
            throw new GraphicsException('Unable to create new image.');

        $result = @imagecopyresampled($destinationImage, $this->resource, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        if ($result === FALSE)
            throw new GraphicsException('Unable to resize image.');

        $this->init($width, $height, $this->type, $destinationImage);

        return $this;
    }

    /**
     * Crop an image to the specified width and height.
     *
     * @param int $width The new width of the cropped image.
     * @param int $height The new height of the cropped image.
     * @param int $x Optional. Set the position from top left.
     * @param int $y Optional. Set the position from top left.
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function crop(int $width, int $height, int $x = 0, int $y = 0): Image
    {
        self::validateWidth($width);
        self::validateHeight($height);

        $result = imagecrop(
            $this->resource,
            ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);

        if ($result === FALSE)
            throw new GraphicsException('Unable to crop image.');

        $this->init($width, $height, $this->type, $result);

        return $this;
    }

    /**
     * Allocate color on image based on ARGB.
     *
     * @param int $red Can be between 0 and 255.
     * @param int $green Can be between 0 and 255.
     * @param int $blue Can be between 0 and 255.
     * @param int $alpha Can be between 0 and 255.
     *
     * @return Color
     *
     * @throws GraphicsException
     */
    public function colorFromARGB(int $red, int $green, int $blue, int $alpha): Color
    {
        return new Color($this->resource, $red, $green, $blue, $alpha);
    }

    /**
     * Allocate color on image based on RGB.
     *
     * @param int $red Can be between 0 and 255.
     * @param int $green Can be between 0 and 255.
     * @param int $blue Can be between 0 and 255.
     *
     * @return Color
     *
     * @throws GraphicsException
     */
    public function colorFromRGB(int $red, int $green, int $blue): Color
    {
        return new Color($this->resource, $red, $green, $blue);
    }

    /**
     * Allocate color on image based on Hex value.
     *
     * @param string $hex Can be between #000000 and #FFFFFF or #00000000 and #FFFFFFFF.
     *
     * @return Color
     *
     * @throws GraphicsException
     */
    public function colorFromHex(string $hex): Color
    {
        if (preg_match('/^\s*#?\s*(?<r>[0-9A-F]{2})(?<g>[0-9A-F]{2})(?<b>[0-9A-F]{2})\s*$/i', $hex, $rgb) === 1) {
            $r = hexdec($rgb['r']);
            $g = hexdec($rgb['g']);
            $b = hexdec($rgb['b']);

            return new Color($this->resource, $r, $g, $b);
        } else if (preg_match('/^\s*#?\s*(?<a>[0-9A-F]{2})(?<r>[0-9A-F]{2})(?<g>[0-9A-F]{2})(?<b>[0-9A-F]{2})\s*$/i', $hex, $rgb)) {
            // convert hex alpha to PHP alpha
            $a = 127 - ceil((127 * hexdec($rgb['a']) / 255));
            $r = hexdec($rgb['r']);
            $g = hexdec($rgb['g']);
            $b = hexdec($rgb['b']);

            return new Color($this->resource, $r, $g, $b, $a);
        } else
            throw new GraphicsException('Unable to create color from Hex.');
    }

    /**
     * Copy the internal resource as a new resource.
     *
     * @resource
     *
     * @throws GraphicsException
     */
    public function getResourceCopy()
    {
        $copy = @imageistruecolor($this->resource) ?
            @imagecreatetruecolor($this->width, $this->height) :
            @imagecreate($this->width, $this->height);

        $result = @imagecopy($copy, $this->resource, 0, 0, 0, 0, $this->width, $this->height);
        if ($result === FALSE)
            throw new GraphicsException('Unable to copy image.');

        return $copy;
    }

    /**
     * @param $radius
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function round($radius): Image
    {
        imageantialias($this->resource, TRUE);
        imagecolortransparent($this->resource, imagecolorallocatealpha($this->resource, 0, 0, 0, 127));
        imagealphablending($this->resource, TRUE);
        imagesavealpha($this->resource, FALSE);

        $corner = self::generateCorner($radius, @imageistruecolor($this->resource));

        // TOP LEFT
        @imagecopymerge($this->resource, $corner, 0, 0, 0, 0, $radius, $radius, 100);

        // TOP RIGHT
        @imageflip($corner, IMG_FLIP_HORIZONTAL);
        @imagecopymerge($this->resource, $corner, $this->width - $radius, 0, 0, 0, $radius, $radius, 100);

        // BOTTOM RIGHT
        @imageflip($corner, IMG_FLIP_VERTICAL);
        @imagecopymerge($this->resource, $corner, $this->width - $radius, $this->height - $radius, 0, 0, $radius, $radius, 100);

        // BOTTOM LEFT
        @imageflip($corner, IMG_FLIP_HORIZONTAL);
        @imagecopymerge($this->resource, $corner, 0, $this->height - $radius, 0, 0, $radius, $radius, 100);

        @imagedestroy($corner);

        return $this;
    }

    /**
     * Draw text on image.
     *
     * @param string $text
     * @param int $x
     * @param int $y
     * @param Color $color
     * @param int $font Can be 1, 2, 3, 4, 5 for built-in fonts in latin2 encoding (where higher numbers corresponding to larger fonts).
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function drawText(string $text, int $x, int $y, Color $color, int $font = 1): Image
    {
        if (imagestring($this->resource, $font, $x, $y, $text, $color->getId()) === FALSE)
            throw new GraphicsException('Unable to write text on image.');

        return $this;
    }

    /**
     * Output image to the browser.
     *
     * @param int $quality
     */
    public abstract function output(int $quality = 85): void;

    /**
     * Initialize image information.
     *
     * @param int $width
     * @param int $height
     * @param int $type
     * @param null $resource
     *
     * @throws GraphicsException
     */
    protected function init(int $width, int $height, int $type, $resource = NULL): void
    {
        $this->validateWidth($width);
        $this->validateHeight($height);

        $this->width = $width;
        $this->height = $height;
        $this->ratio = $width / $height;
        $this->type = $type;

        if ($this->resource != NULL && is_resource($this->resource) && (@imagedestroy($this->resource) === FALSE))
            throw new GraphicsException('Unable to destroy image.');

        $this->resource = ($resource == NULL) ? @imagecreatetruecolor($width, $height) : $resource;
    }
}