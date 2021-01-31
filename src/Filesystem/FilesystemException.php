<?php

declare(strict_types=1);

namespace Philly\Filesystem;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class FilesystemException.
 */
abstract class FilesystemException extends Exception
{
    #[Pure] public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
