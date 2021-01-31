<?php

declare(strict_types=1);

namespace Philly\Contracts\Support;

use Countable;

/**
 * Interface Storage.
 */
interface Storage extends Countable, JsonCompatible
{
    /**
     * @return bool Whether this storage is empty.
     */
    public function isEmpty(): bool;

    /**
     * @inheritDoc
     */
    public function count(): int;

    /**
     * Clears the storage.
     */
    public function clear(): void;

    /**
     * Copies this storage.
     *
     * @param bool $deep Whether to do a deep copy (also clones every single elements).
     * @return Storage A new storage instance containing similar elements as the original.
     */
    public function copy(bool $deep = true): self;
}
