<?php

namespace AntonioKadid\WAPPKitCore\Graphics;

use AntonioKadid\WAPPKitCore\Graphics\Exceptions\GraphicsException;
use InvalidArgumentException;

/**
 * Class Color
 *
 * Only for true color images.
 *
 * @package AntonioKadid\WAPPKitCore\Graphics
 */
class Color
{
    const AliceBlue = 15792383;
    const AntiqueWhite = 16444375;
    const Aqua = 65535;
    const Aquamarine = 8388564;
    const Azure = 15794175;
    const Beige = 16119260;
    const Bisque = 16770244;
    const Black = 0;
    const BlanchedAlmond = 16772045;
    const Blue = 255;
    const BlueViolet = 9055202;
    const Brown = 10824234;
    const BurlyWood = 14596231;
    const CadetBlue = 6266528;
    const Chartreuse = 8388352;
    const Chocolate = 13789470;
    const Coral = 16744272;
    const CornflowerBlue = 6591981;
    const Cornsilk = 16775388;
    const Crimson = 14423100;
    const Cyan = 65535;
    const DarkBlue = 139;
    const DarkCyan = 35723;
    const DarkGoldenrod = 12092939;
    const DarkGray = 11119017;
    const DarkGreen = 25600;
    const DarkKhaki = 12433259;
    const DarkMagenta = 9109643;
    const DarkOliveGreen = 5597999;
    const DarkOrange = 16747520;
    const DarkOrchid = 10040012;
    const DarkRed = 9109504;
    const DarkSalmon = 15308410;
    const DarkSeaGreen = 9419919;
    const DarkSlateBlue = 4734347;
    const DarkSlateGray = 3100495;
    const DarkTurquoise = 52945;
    const DarkViolet = 9699539;
    const DeepPink = 16716947;
    const DeepSkyBlue = 49151;
    const DimGray = 6908265;
    const DodgerBlue = 2003199;
    const Firebrick = 11674146;
    const FloralWhite = 16775920;
    const ForestGreen = 2263842;
    const Fuchsia = 16711935;
    const Gainsboro = 14474460;
    const GhostWhite = 16316671;
    const Gold = 16766720;
    const Goldenrod = 14329120;
    const Gray = 8421504;
    const Green = 32768;
    const GreenYellow = 11403055;
    const Honeydew = 15794160;
    const HotPink = 16738740;
    const IndianRed = 13458524;
    const Indigo = 4915330;
    const Ivory = 16777200;
    const Khaki = 15787660;
    const Lavender = 15132410;
    const LavenderBlush = 16773365;
    const LawnGreen = 8190976;
    const LemonChiffon = 16775885;
    const LightBlue = 11393254;
    const LightCoral = 15761536;
    const LightCyan = 14745599;
    const LightGoldenrodYellow = 16448210;
    const LightGray = 13882323;
    const LightGreen = 9498256;
    const LightPink = 16758465;
    const LightSalmon = 16752762;
    const LightSeaGreen = 2142890;
    const LightSkyBlue = 8900346;
    const LightSlateGray = 7833753;
    const LightSteelBlue = 11584734;
    const LightYellow = 16777184;
    const Lime = 65280;
    const LimeGreen = 3329330;
    const Linen = 16445670;
    const Magenta = 16711935;
    const Maroon = 8388608;
    const MediumAquamarine = 6737322;
    const MediumBlue = 205;
    const MediumOrchid = 12211667;
    const MediumPurple = 9662683;
    const MediumSeaGreen = 3978097;
    const MediumSlateBlue = 8087790;
    const MediumSpringGreen = 64154;
    const MediumTurquoise = 4772300;
    const MediumVioletRed = 13047173;
    const MidnightBlue = 1644912;
    const MintCream = 16121850;
    const MistyRose = 16770273;
    const Moccasin = 16770229;
    const NavajoWhite = 16768685;
    const Navy = 128;
    const OldLace = 16643558;
    const Olive = 8421376;
    const OliveDrab = 7048739;
    const Orange = 16753920;
    const OrangeRed = 16729344;
    const Orchid = 14315734;
    const PaleGoldenrod = 15657130;
    const PaleGreen = 10025880;
    const PaleTurquoise = 11529966;
    const PaleVioletRed = 14381203;
    const PapayaWhip = 16773077;
    const PeachPuff = 16767673;
    const Peru = 13468991;
    const Pink = 16761035;
    const Plum = 14524637;
    const PowderBlue = 11591910;
    const Purple = 8388736;
    const Red = 16711680;
    const RosyBrown = 12357519;
    const RoyalBlue = 4286945;
    const SaddleBrown = 9127187;
    const Salmon = 16416882;
    const SandyBrown = 16032864;
    const SeaGreen = 3050327;
    const SeaShell = 16774638;
    const Sienna = 10506797;
    const Silver = 12632256;
    const SkyBlue = 8900331;
    const SlateBlue = 6970061;
    const SlateGray = 7372944;
    const Snow = 16775930;
    const SpringGreen = 65407;
    const SteelBlue = 4620980;
    const Tan = 13808780;
    const Teal = 32896;
    const Thistle = 14204888;
    const Tomato = 16737095;
    const Transparent = 2130706432;
    const Turquoise = 4251856;
    const Violet = 15631086;
    const Wheat = 16113331;
    const White = 16777215;
    const WhiteSmoke = 16119285;
    const Yellow = 16776960;
    const YellowGreen = 10145074;

    /** @var string */
    private $index;
    /** @var int|NULL */
    private $alpha;
    /** @var int */
    private $red;
    /** @var int */
    private $green;
    /** @var int */
    private $blue;

    /**
     * Color constructor.
     *
     * @param int      $red   Can be between 0 and 255.
     * @param int      $green Can be between 0 and 255.
     * @param int      $blue  Can be between 0 and 255.
     * @param int|NULL $alpha Can be between 0 and 255 or NULL.
     */
    public function __construct(int $red, int $green, int $blue, ?int $alpha = NULL)
    {
        $this->validate($red);
        $this->red = $red;

        $this->validate($green);
        $this->green = $green;

        $this->validate($blue);
        $this->blue = $blue;

        if (!is_null($alpha))
            $this->validate($alpha);
        $this->alpha = $alpha;

        $this->calculateIndex();
    }

    /**
     * Create a new color based on ARGB format.
     *
     * @param int $alpha Can be between 0 and 255.
     * @param int $red   Can be between 0 and 255.
     * @param int $green Can be between 0 and 255.
     * @param int $blue  Can be between 0 and 255.
     *
     * @return Color
     */
    public static function fromARGB(int $alpha, int $red, int $green, int $blue): Color
    {
        return new Color($red, $green, $blue, $alpha);
    }

    /**
     * Create a new color based on RGB format.
     *
     * @param int $red   Can be between 0 and 255.
     * @param int $green Can be between 0 and 255.
     * @param int $blue  Can be between 0 and 255.
     *
     * @return Color
     */
    public static function fromRGB(int $red, int $green, int $blue): Color
    {
        return new Color($red, $green, $blue);
    }

    /**
     * Create a new color based on RGBA format.
     *
     * @param int   $red   Can be between 0 and 255.
     * @param int   $green Can be between 0 and 255.
     * @param int   $blue  Can be between 0 and 255.
     * @param float $alpha Can be between 0 and 1. 0 transparent and 1 opaque.
     *
     * @return Color
     */
    public static function fromRGBA(int $red, int $green, int $blue, float $alpha): Color
    {
        return Color::fromARGB(ceil(255 * $alpha), $red, $green, $blue);
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
    public static function fromHex(string $hex): Color
    {
        if (preg_match('/^\s*#?\s*(?<r>[0-9A-F]{2})(?<g>[0-9A-F]{2})(?<b>[0-9A-F]{2})\s*$/i', $hex, $rgb) === 1) {
            $r = hexdec($rgb['r']);
            $g = hexdec($rgb['g']);
            $b = hexdec($rgb['b']);

            return new Color($r, $g, $b);
        } else if (preg_match('/^\s*#?\s*(?<a>[0-9A-F]{2})(?<r>[0-9A-F]{2})(?<g>[0-9A-F]{2})(?<b>[0-9A-F]{2})\s*$/i', $hex, $rgb)) {
            // convert hex alpha to PHP alpha
            $a = hexdec($rgb['a']);
            $r = hexdec($rgb['r']);
            $g = hexdec($rgb['g']);
            $b = hexdec($rgb['b']);

            return new Color($r, $g, $b, $a);
        } else
            throw new GraphicsException('Unable to create color from Hex.');
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return int|NULL
     */
    public function getAlpha(): ?int
    {
        return $this->alpha;
    }

    /**
     * @param int|NULL $alpha
     */
    public function setAlpha(?int $alpha): void
    {
        if (!is_null($alpha))
            $this->validate($alpha);
        $this->alpha = $alpha;
        $this->calculateIndex();
    }

    /**
     * @return int
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * @param int $red
     */
    public function setRed(int $red): void
    {
        $this->validate($red);
        $this->red = $red;
        $this->calculateIndex();
    }

    /**
     * @return int
     */
    public function getGreen(): int
    {
        return $this->green;
    }

    /**
     * @param int $green
     */
    public function setGreen(int $green): void
    {
        $this->validate($green);
        $this->green = $green;
        $this->calculateIndex();
    }

    /**
     * @return int
     */
    public function getBlue(): int
    {
        return $this->blue;
    }

    /**
     * @param int $blue
     */
    public function setBlue(int $blue): void
    {
        $this->validate($blue);
        $this->blue = $blue;
        $this->calculateIndex();
    }

    /**
     * @param int $value
     */
    private function validate(int $value): void
    {
        if ($value < 0 || $value > 255)
            throw new InvalidArgumentException('Invalid value for color. Minimum value is 0 and maximum value is 255.');
    }

    /**
     * Calculate the index used by PHP to identify the color.
     * This index is only for true color images.
     */
    private function calculateIndex(): void
    {
        if (is_null($this->alpha)) {
            $this->index = hexdec(sprintf('00%02X%02X%02X', $this->red, $this->green, $this->blue));
            return;
        }

        // convert alpha to PHP alpha (0 opaque - 127 transparent)
        $alpha = 127 - ceil((127 * $this->alpha / 255));
        $this->index = hexdec(sprintf('%02X%02X%02X%02X', $alpha, $this->red, $this->green, $this->blue));
    }
}