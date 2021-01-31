<?php

declare(strict_types=1);

namespace Philly\Container;

use Philly\Contracts\Container\Collection as CollectionContract;

/**
 * Class Collection
 */
class Collection extends Container implements CollectionContract
{
    /**
     * @var int
     */
    private int $nextOffset = 0;

    /**
     * Collection constructor.
     *
     * @param array $items Items, which should be contained in the collection.
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    /**
     * @inheritDoc
     */
    public function acceptsKey($offset): bool
    {
        return is_int($offset);
    }

    /**
     * @inheritDoc
     */
    public function add($value): self
    {
        $this->offsetSet($this->nextOffset, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if (is_int($offset) && $offset >= $this->nextOffset) {
            $this->nextOffset = $offset + 1;
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if (is_numeric($offset) && $offset == $this->nextOffset - 1) {
            $this->nextOffset = $offset;
        }

        parent::offsetUnset($offset);
    }

    /**
     * Get the offset for the next element to be added.
     *
     * @return int
     */
    protected function getNextOffset(): int
    {
        return $this->nextOffset;
    }

    /**
     * @inheritDoc
     */
    public function where(callable $callback, bool $preserve_keys = true): self
    {
        $items = array_filter($this->storage, $callback, ARRAY_FILTER_USE_BOTH);

        if (!$preserve_keys) {
            $items = array_values($items);
        }

        return new Collection($items);
    }

    /**
     * @inheritDoc
     */
    public function first(?callable $callback = null, $default = null)
    {
        if ($this->count() > 0) {
            if ($callback === null) {
                // reset() will not return false since the array is not empty
                return reset($this->storage);
            }

            foreach ($this->storage as $key => $value) {
                if (($callback($value, $key)) === true) {
                    return $value;
                }
            }
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function any(?callable $callback = null): bool
    {
        if ($callback === null) {
            return $this->count() > 0;
        }

        return $this->first($callback) !== null;
    }

    /**
     * @inheritDoc
     */
    public function asArray(): array
    {
        return $this->storage;
    }
}
