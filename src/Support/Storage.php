<?php

declare(strict_types=1);

namespace Philly\Support;

use Philly\Contracts\Support\Storage as StorageContract;

/**
 * Class Storage.
 */
class Storage implements StorageContract
{
    use JsonCompatible;

    protected array $storage;

    /**
     * Storage constructor.
     */
    protected function __construct(array $items = [])
    {
        $this->storage = $items;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return $this->count() == 0;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->storage);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->storage;
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        unset($this->storage);

        $this->storage = [];
    }

    /**
     * @inheritDoc
     */
    public function copy(bool $deep = true): StorageContract
    {
        if (!$deep) {
            return new self($this->storage);
        }

        $copy = new self();
        foreach ($this->storage as $k => $v) {
            $copy->storage[$k] = clone $v;
        }

        return $copy;
    }
}
