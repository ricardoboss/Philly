<?php

declare(strict_types=1);

namespace Philly\Contracts\Container;

use ArrayAccess;
use IteratorAggregate;
use Philly\Contracts\Support\Storage;
use Psr\Container\ContainerInterface;
use Traversable;

/**
 * Interface Container.
 */
interface Container extends ContainerInterface, ArrayAccess, Traversable, IteratorAggregate, Storage
{
    /**
     * Store a value with an associated key.
     *
     * @param int|string|float $key
     * @param mixed $value
     */
    public function put($key, $value): void;

    /**
     * Get all keys available in the container.
     */
    public function getKeys(): array;

    /**
     * Get all values available in the container.
     */
    public function getValues(): array;

    /**
     * Get a value from the container or a default value if the key doesn't exist.
     * Implementations should store the default value with the given key if it didn't exist yet, hence the "lazy" term.
     *
     * Multiple calls to this method with the same key should result in the same outputs.
     *
     * @param int|string|float $key
     * @param mixed $default
     * @return mixed
     */
    public function getLazy($key, $default);

    /**
     * Check whether this container accepts the given value. This method should be overridden by implementations that
     * check the types of values added to this container.
     *
     * @param mixed $value The value to check.
     *
     * @return bool
     */
    public function accepts($value): bool;

    /**
     * Check whether this contains accepts the given value as a key. This method should be overridden by implementations
     * that restrict the types of keys for this container.
     *
     * @param string|int|float|null $offset
     * @return bool
     */
    public function acceptsKey($offset): bool;

    /**
     * @inheritDoc
     */
    public function getIterator(): ContainerIterator;

    /**
     * Copies this container and its contents.
     *
     * @param bool $deep Whether to do a deep copy (also clones every single elements).
     * @return Container A new container instance containing similar elements as the original.
     */
    public function copy(bool $deep = true): self;
}
