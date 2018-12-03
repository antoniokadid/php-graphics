<?php

namespace Graphics;

/**
 * Class Color
 *
 * @package Graphics
 */
class Color
{
    private $id;
    private $red;
    private $green;
    private $blue;
    private $alpha;
    private $resource;

    /**
     * Color constructor.
     *
     * @param $resource
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param int|NULL $alpha
     *
     * @throws GraphicsException
     */
    public function __construct($resource, int $red, int $green, int $blue, ?int $alpha = NULL)
    {
        if (!is_resource($resource))
            throw new GraphicsException('Invalid resource.');

        $color = $alpha == NULL ?
            @imagecolorallocate($resource, $red, $green, $blue) :
            @imagecolorallocatealpha($resource, $red, $green, $blue, $alpha);

        if ($color === FALSE)
            throw new GraphicsException('Unable to allocate color.');

        $this->resource = $resource;

        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $alpha;

        $this->id = $color;
    }

    public function __destruct()
    {
        if ($this->resource != NULL && is_resource($this->resource))
            @imagecolordeallocate($this->resource, $this->id);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * @return int
     */
    public function getGreen(): int
    {
        return $this->green;
    }

    /**
     * @return int
     */
    public function getBlue(): int
    {
        return $this->blue;
    }

    /**
     * @return int|NULL
     */
    public function getAlpha(): ?int
    {
        return $this->alpha;
    }
}