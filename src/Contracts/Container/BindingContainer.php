<?php

declare(strict_types=1);

namespace Philly\Contracts\Container;

use Closure;

/**
 * Interface BindingContainer
 */
interface BindingContainer extends Container
{
    /**
     * Bind a builder callback to a contract.
     *
     * @param Closure|mixed $builder Can be a callable or an instance which implements the given interface.
     * @return BindingContract The contract which binds a builder/instance to an interface.
     */
    public function bind(string $interface, $builder, bool $singleton = false): BindingContract;

    /**
     * Whether or not this binding container binds this object. Implementations of BindingContainers should overwrite
     * this method to constrain which types are bound by this container.
     *
     * @param mixed $value The object to check.
     *
     * @return bool
     */
    public function acceptsBinding($value): bool;

    /**
     * Get a value from the container or a default value if the key doesn't exist.
     * Implementations should store the default value with the given key if it didn't exist yet, hence the "lazy" term.
     *
     * Multiple calls to this method with the same key result in the same outputs.
     *
     * @param int|string|float $key
     * @param Closure|mixed $default A builder or the default instance. The builder will only be called if the key does
     *                               not exist in the container.
     * @return mixed
     */
    public function getLazySingleton($key, $default);
}
