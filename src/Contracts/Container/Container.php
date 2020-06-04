<?php
declare(strict_types=1);

namespace Philly\Contracts\Container;

use ArrayAccess;
use IteratorAggregate;
use Philly\Contracts\Support\JsonCompatible;
use Psr\Container\ContainerInterface;
use Traversable;

/**
 * Interface Container
 */
interface Container extends ContainerInterface, ArrayAccess, Traversable, IteratorAggregate, JsonCompatible
{
    /**
     * Store a value with an associated key.
     * @param int|string|float $key
     * @param mixed $value
     */
    function put($key, $value): void;

    /**
     * Get all keys available in the container.
     */
    function getKeys(): array;

    /**
     * Get all values available in the container.
     */
    function getValues(): array;

    /**
     * Get a value from the container or a default value if the key doesn't exist.
     * Implementations should store the default value with the given key if it didn't exist yet, hence the "lazy" term.
     *
     * Multiple calls to this method with the same(!) arguments should result in the same outputs.
     * @param int|string|float $key
     * @param mixed $default
     * @return mixed
     */
    function getLazy($key, $default);


	/**
	 * Check whether this container accepts the given value. This method should be overridden for implementations that
	 * check the types of values added to this container.
	 *
	 * @param mixed $value The value to check.
	 *
	 * @return bool
	 */
	function accepts($value): bool;

	/**
	 * @inheritDoc
	 */
    function getIterator(): ContainerIterator;
}
