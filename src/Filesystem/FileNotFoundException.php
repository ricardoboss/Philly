<?php

declare(strict_types=1);

namespace Philly\Filesystem;

use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class FileNotFoundException.
 */
class FileNotFoundException extends FilesystemException
{
    protected ?string $path;

    #[Pure] public function __construct($message = null, $code = 0, Throwable $previous = null, ?string $path = null)
    {
        if ($message === null) {
            if ($path === null) {
                $message = "File not found.";
            } else {
                $message = "File at $path not found.";
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * The path of the file, if given.
     */
    public function getPath(): ?string
    {
        return $this->path;
    }
}
