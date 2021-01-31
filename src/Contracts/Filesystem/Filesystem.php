<?php

declare(strict_types=1);

namespace Philly\Contracts\Filesystem;

use Philly\Filesystem\FileExistsException;
use Philly\Filesystem\FileNotCreatedException;
use Philly\Filesystem\FileNotFoundException;

/**
 * Interface Filesystem.
 */
interface Filesystem
{
    /**
     * @return string The name of this filesystem.
     */
    public function getName(): string;

    /**
     * @return string The root of this filesystem.
     */
    public function getRoot(): string;

    /**
     * Checks whether a path exists in the filesystem.
     *
     * @param string $path The path to check.
     * @return bool Whether the path exists in the filesystem.
     */
    public function exists(string $path): bool;

    /**
     * Gets the contents of a file in the filesystem.
     *
     * @param string $path The path of the file within the filesystem.
     *
     * @return string The contents of a file.
     *
     * @throws FileNotFoundException if the file does not exist.
     */
    public function getContents(string $path): string;

    /**
     * Writes contents into a file within the filesystem.
     *
     * @param string $path The path where to write the file.
     * @param string $content The contents to write into the file.
     * @param bool $overwrite Whether to overwrite the file.
     *
     * @return bool Whether the operation was successful.
     *
     * @throws FileExistsException if the file already exist and $overwrite is false.
     * @throws FileNotCreatedException if the file could not be created.
     */
    public function putContents(string $path, string $content, bool $overwrite = true): bool;

    /**
     * Convert a relative path within the filesystem to a real, physical path.
     *
     * @param string $path The path to resolve.
     * @param bool $throw Whether to throw an exception if the file cannot be found.
     *
     * @return string|null The real path or null if no exception shall be thrown.
     *
     * @throws FileNotFoundException if the file does not exist.
     */
    public function real(string $path, bool $throw = true): ?string;
}
