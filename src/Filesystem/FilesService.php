<?php

declare(strict_types=1);

namespace Philly\Filesystem;

use InvalidArgumentException;
use Philly\Contracts\Filesystem\FilesService as FilesServiceContract;
use Philly\Contracts\Filesystem\Filesystem as FilesystemContract;
use Philly\Contracts\Filesystem\FilesystemContainer as FilesystemContainerContract;
use Philly\Exceptions\NullReferenceException;
use Philly\ServiceProvider\ServiceProvider;
use ricardoboss\Console;

/**
 * Class FilesService.
 */
class FilesService extends ServiceProvider implements FilesServiceContract
{
    protected FilesystemContainerContract $storage;

    /**
     * FilesService constructor.
     */
    public function __construct()
    {
        $this->storage = new FilesystemContainer();
    }

    /**
     * @inheritDoc
     */
    public function onRegistered(): void
    {
        $this->add('philly-root', dirname(__DIR__, 2));

        parent::onRegistered();
    }

    /**
     * @inheritDoc
     */
    public function getAll(): FilesystemContainerContract
    {
        return $this->storage->copy(deep: true);
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): FilesystemContract
    {
        return $this->storage->offsetGet($name);
    }

    /**
     * @inheritDoc
     */
    public function add(string|FilesystemContract $filesystem, ?string $root = null): void
    {
        if ($filesystem instanceof FilesystemContract) {
            if ($root !== null) {
                throw new InvalidArgumentException("Cannot add filesystem instance with different root!");
            }

            $name = $filesystem->getName();
            $fs = $filesystem;
        } else {
            if ($root === null) {
                throw new NullReferenceException(var_name: 'root');
            }

            $name = $filesystem;
            $fs = new Filesystem($name, $root);
        }

        $this->storage[$name] = $fs;

        Console::debug("Added filesystem '$name' with root '{$fs->getRoot()}'");
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return $this->storage->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->storage->offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->storage->offsetUnset($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->storage->offsetGet($offset);
    }
}
