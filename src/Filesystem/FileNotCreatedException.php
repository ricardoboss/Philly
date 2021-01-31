<?php

declare(strict_types=1);

namespace Philly\Filesystem;

use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class FileNotCreatedException.
 */
class FileNotCreatedException extends FilesystemException
{
    protected ?string $path;

    #[Pure] public function __construct($message = null, $code = 0, Throwable $previous = null, ?string $path = null)
    {
        if ($message === null) {
            if ($path === null) {
                $message = "File could not be created.";
            } else {
                $message = "File at $path could not be created.";
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
