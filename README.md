# WAPPKit Core - Graphics
A PHP library to process true color images.

Part of Web Application Kit (WAPPKit) Core which powers WAPPKit, a privately owned CMS.

*Project under development and may be subject to a lot of changes. Use at your own risk.*

## Installation

composer require antoniokadid/wappkit-core-graphics

## Requirements
* PHP 7.1
* GD Extension

## Features

#### Images
* Load image from file.
* Load image from Base64
* Resize
* Crop
* Draw text over images
* Draw images over images
* Create images with round borders
* Flip images horizontally and vertically
* Supported image types:
  * JPEG
  * BMP
  * PNG

#### Colors
* Create color from RGB, ARGB, RGBA and HEX.
* HTML colors as constants with valid GD color index (true color images only).

## Examples

##### Resize an image

```php
use AntonioKadid\WAPPKitCore\Graphics\Image;

// Load test.jpg resize it with width 200 pixels and adjust height automatically.
Image::fromFile('test.jpg')
    ->resizeToWidth(200)
    ->asJpg();

// Load test.jpg resize it with height 200 pixels and adjust width automatically.
Image::fromFile('test.jpg')
    ->resizeToHeight(200)
    ->asJpg();

// Load test.jpg resize it to 200x300. Ratio is not preserved.
Image::fromFile('test.jpg')
    ->resize(200, 300)
    ->asJpg();
```

##### Crop an image

```php
use AntonioKadid\WAPPKitCore\Graphics\Image;

Image::fromFile('test.jpg')
    ->crop(50, 50)
    ->asJpg();
```

## LICENSE

MIT license.