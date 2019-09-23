<?php

namespace AntonioKadid\Graphics;

use InvalidArgumentException;

/**
 * Class Image
 *
 * @package AntonioKadid\Graphics
 */
class Image
{
    /** @var int */
    protected $width;
    /** @var int */
    protected $height;
    /** @var float */
    protected $ratio;
    /** @var int */
    protected $type;
    /** @var resource */
    protected $resource;

    /**
     * Image constructor.
     *
     * @param int $width
     * @param int $height
     * @param resource|NULL $resource
     * @param int $type
     */
    public function __construct(int $width = 0, int $height = 0, $resource = NULL, int $type = IMAGETYPE_UNKNOWN)
    {
        if (is_resource($resource)) {
            $this->resource = $resource;
            $this->type = $type;
            $this->width = @imagesx($resource);
            $this->height = @imagesy($resource);
            $this->ratio = $this->width / $this->height;
        } else {
            if ($width <= 0)
                throw new InvalidArgumentException('Width cannot be less than or equal to 0.');

            if ($height <= 0)
                throw new InvalidArgumentException('Height cannot be less than or equal to 0.');

            $this->resource = @imagecreatetruecolor($width, $height);
            $this->type = $type;
            $this->width = @imagesx($this->resource);
            $this->height = @imagesy($this->resource);
            $this->ratio = $this->width / $this->height;
        }

        @imagealphablending($this->resource, FALSE);
        @imagesavealpha($this->resource, TRUE);
    }

    public function __destruct()
    {
        if (is_resource($this->resource))
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
     * @return float
     */
    public function getRatio(): float
    {
        return $this->ratio;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Create a new blank image.
     *
     * @param int $width
     * @param int $height
     * @param int $type
     *
     * @return Image
     */
    public static function blank(int $width, int $height, int $type = IMAGETYPE_UNKNOWN): Image
    {
        return new Image($width, $height, NULL, $type);
    }

    /**
     * Use an existing resource as an image.
     *
     * @param $resource
     * @param int $type
     *
     * @return Image
     */
    public static function fromResource($resource, int $type = IMAGETYPE_UNKNOWN)
    {
        return new Image(0, 0, $resource, $type);
    }

    /**
     * @param string $filename
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public static function fromFile($filename): Image
    {
        if (!file_exists($filename))
            throw new GraphicsException(sprintf('%s does not exist.', $filename));

        $handle = fopen($filename, 'r');
        $data = fread($handle, filesize($filename));
        fclose($handle);

        return self::fromData($data);
    }

    /**
     * @param string $base64
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public static function fromBase64(string $base64): Image
    {
        $data = base64_decode($base64);
        if ($data === FALSE)
            throw new GraphicsException('Unable to read Base64 data.');

        return self::fromData($data);
    }

    /**
     * @param $data
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public static function fromData(string $data): Image
    {
        $resource = @imagecreatefromstring($data);
        if ($resource === FALSE)
            throw new GraphicsException('Unable to process image data.');

        $size = getimagesizefromstring($data, $info);
        if ($size === FALSE)
            throw new GraphicsException('Unable to process image data.');

        list($width, $height, $type, $attr) = $size;

        return new Image($width, $height, $resource, $type);
    }

    /**
     * @return Image
     *
     * @throws GraphicsException
     */
    public function clone(): Image
    {
        $image = new Image($this->width, $this->height, NULL, $this->type);

        $result = @imagecopy($image->resource, $this->resource, 0, 0, 0, 0, $this->width, $this->height);
        if ($result === FALSE)
            throw new GraphicsException('Unable to copy image.');

        return $image;
    }

    /**
     * Flood fill.
     *
     * @param int $color
     * @param int $x x-coordinate of start point
     * @param int $y y-coordinate of start point
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function fill(int $color, int $x = 0, int $y = 0): Image
    {
        if (@imagefill($this->resource, $x, $y, $color) === FALSE)
            throw new GraphicsException('Unable to fill image.');

        return $this;
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
        if ($width <= 0)
            throw new InvalidArgumentException('Width cannot be less than or equal to 0.');

        $height = ceil($width / $this->ratio);

        return $this->resize($width, $height);
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
        if ($height <= 0)
            throw new InvalidArgumentException('Height cannot be less than or equal to 0.');

        $width = ceil($height * $this->ratio);

        return $this->resize($width, $height);
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
        $image = new Image($width, $height, NULL, $this->type);

        if (@imagecopyresampled($image->resource, $this->resource, 0, 0, 0, 0, $width, $height, $this->width, $this->height) === FALSE)
            throw new GraphicsException('Unable to resize image.');

        return $image;
    }

    /**
     * Crop an image to the specified width and height.
     *
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function crop(int $width, int $height, int $x = 0, int $y = 0): Image
    {
        if ($width <= 0)
            throw new InvalidArgumentException('Width cannot be less than or equal to 0.');

        if ($height <= 0)
            throw new InvalidArgumentException('Height cannot be less than or equal to 0.');

        $result = imagecrop($this->resource, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
        if ($result === FALSE)
            throw new GraphicsException('Unable to crop image.');

        return new Image($width, $height, $result, $this->type);
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
        $corner = Image::blank($radius, $radius);
        // Fill the image with the base transparent color.
        @imagefill($corner->resource, 0, 0, Color::Transparent);
        // Draw a black filled arc.
        @imagefilledarc($corner->resource, $radius, $radius, $radius * 2, $radius * 2, 180, 270, Color::Black, IMG_ARC_PIE);
        // Make the black color as transparent (only for $corner). This will make the arc transparent.
        @imagecolortransparent($corner->resource, Color::Black);

        // Add $corner to the corners of the main image.

        // Set the transparent color to the base transparent color.
        @imagecolortransparent($this->resource, Color::Transparent);

        // TOP LEFT
        $this->drawImage($corner);

        // TOP RIGHT
        $corner->flipHorizontal();
        $this->drawImage($corner, $this->width - $radius, 0);

        // BOTTOM RIGHT
        $corner->flipVertical();
        $this->drawImage($corner, $this->width - $radius, $this->height - $radius);

        // BOTTOM LEFT
        $corner->flipHorizontal();
        $this->drawImage($corner, 0, $this->height - $radius);

        return $this;
    }

    /**
     * Mirror an image horizontally.
     *
     * @return Image
     */
    public function flipHorizontal(): Image
    {
        @imageflip($this->resource, IMG_FLIP_HORIZONTAL);

        return $this;
    }

    /**
     * Mirror an image vertically.
     *
     * @return Image
     */
    public function flipVertical(): Image
    {
        @imageflip($this->resource, IMG_FLIP_VERTICAL);

        return $this;
    }

    /**
     * Draw text.
     *
     * @param string $text
     * @param int $x
     * @param int $y
     * @param int $color
     * @param int $font Can be 1, 2, 3, 4, 5 for built-in fonts in latin2 encoding (where higher numbers corresponding to larger fonts).
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function drawText(string $text, int $x, int $y, int $color, int $font = 1): Image
    {
        if (@imagestring($this->resource, $font, $x, $y, $text, $color) === FALSE)
            throw new GraphicsException('Unable to write text on image.');

        return $this;
    }

    /**
     * Draw an image.
     *
     * @param Image $image
     * @param int $x
     * @param int $y
     *
     * @return Image
     *
     * @throws GraphicsException
     */
    public function drawImage(Image $image, int $x = 0, int $y = 0): Image
    {
        if (@imagecopy($this->resource, $image->resource, $x, $y, 0, 0, $image->width, $image->height) === FALSE)
            throw new GraphicsException('Unable to draw image on image.');

        return $this;
    }

    /**
     * Output as BMP.
     *
     * @param mixed|NULL $to The path to save the file to. If not set or &null;, the raw image stream will be outputted directly.
     * @param bool $compressed
     *
     * @return bool
     */
    public function asBmp($to = NULL, bool $compressed = TRUE): bool
    {
        if ($to == NULL) {
            if (ob_get_length() !== FALSE)
                ob_clean();
            header_remove('Content-Type');
            header('Content-Type: image/bmp');
        }

        return @imagebmp($this->resource, $to, $compressed);
    }

    /**
     * Output as JPEG.
     *
     * @param mixed|null $to The path to save the file to. If not set or &null;, the raw image stream will be outputted directly.
     * @param int $quality From 0 (worst quality, smaller file) to 100 (best quality, biggest file).
     *
     * @return bool
     */
    public function asJpg($to = NULL, int $quality = 75): bool
    {
        if ($quality < 0)
            $quality = 0;
        else if ($quality > 100)
            $quality = 100;

        if ($to == NULL) {
            if (ob_get_length() !== FALSE)
                ob_clean();
            header_remove('Content-Type');
            header('Content-Type: image/jpeg');
        }

        return @imagejpeg($this->resource, $to, $quality);
    }

    /**
     * Output as PNG.
     *
     * @param mixed|null $to The path to save the file to. If not set or &null;, the raw image stream will be outputted directly.
     * @param int $quality Compression level: from 0 (no compression) to 9 (default).
     * @param $filters
     *
     * @return bool
     */
    public function asPng($to = NULL, int $quality = 9, ?int $filters = NULL)
    {
        if ($quality < 0)
            $quality = 0;
        else if ($quality > 9)
            $quality = 9;

        if ($to == NULL) {
            if (ob_get_length() !== FALSE)
                ob_clean();
            header_remove('Content-Type');
            header('Content-Type: image/png');
        }

        return @imagepng($this->resource, $to, $quality, $filters);
    }
}