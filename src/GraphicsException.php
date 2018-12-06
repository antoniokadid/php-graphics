<?php

namespace Graphics;

use Throwable;

/**
 * Class GraphicsException
 *
 * @package Graphics
 */
class GraphicsException extends \Exception
{
    /**
     * GraphicsException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}