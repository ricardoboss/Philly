<?php

declare(strict_types=1);

namespace Philly\Contracts\Filesystem;

use ArrayAccess;
use InvalidArgumentException;
use Philly\Contracts\Filesystem\Filesystem as FilesystemContract;
use Philly\Contracts\Filesystem\FilesystemContainer as FilesystemContainerContract;
use Philly\Contracts\ServiceProvider\ServiceProvider;

/**
 * Interface FilesService.
 */
interface FilesService extends ServiceProvider, ArrayAccess
{
    /**
     * Returns all registered filesystems.
     *
     * @return FilesystemContainerContract A container with filesystems.
     */
    public function getAll(): FilesystemContainerContract;

    /**
     * Gets a specific filesystem by name.
     *
     * @param string $name The name of the filesystem.
     *
     * @return Filesystem The filesystem.
     *
     * @throws InvalidArgumentException If the name cannot be resolved to a filesystem.
     */
    public function get(string $name): Filesystem;

    /**
     * Adds a new filesystem under a specific name.
     *
     * @param Filesystem|string $filesystem The name of the filesystem or an existing filesystem.
     * @param string|null $root The root of a filesystem or null if the first parameter is a filesystem instance.
     */
    public function add(string|FilesystemContract $filesystem, ?string $root = null): void;
}
