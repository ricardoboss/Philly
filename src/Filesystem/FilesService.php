<?php
declare(strict_types=1);

namespace Philly\Filesystem;

use Philly\Container\Container;
use Philly\Contracts\Container\Container as ContainerContract;
use Philly\Contracts\Filesystem\FilesService as FilesServiceContract;
use Philly\Contracts\Filesystem\Filesystem as FilesystemContract;
use Philly\ServiceProvider\ServiceProvider;

class FilesService extends ServiceProvider implements FilesServiceContract
{
    protected ContainerContract $storage;

    public function __construct()
    {
        $this->storage = new Container();
    }

    /**
     * @inheritDoc
     * @throws FileNotFoundException
     */
    public function onRegistered(): void
    {
        $this->add('philly-root', dirname(__DIR__, 2));
        $this->add('philly-command-stubs', $this->get('philly-root')->real("src/Foundation/CLI/Commands/stubs/"));

        parent::onRegistered();
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ContainerContract
    {
        return $this->storage->copy();
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): FilesystemContract
    {
        return $this->storage[$name];
    }

    /**
     * @inheritDoc
     */
    public function add(string $name, string|FilesystemContract $filesystem): void
    {
        if ($filesystem instanceof FilesystemContract)
            $this->storage[$name] = $filesystem;
        else
            $this->storage[$name] = new Filesystem($name, $filesystem);
    }

    public function offsetExists($offset): bool
    {
        return $this->storage->offsetExists($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->storage->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->storage->offsetUnset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->storage->offsetGet($offset);
    }
}