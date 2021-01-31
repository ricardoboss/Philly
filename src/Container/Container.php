<?php

declare(strict_types=1);

namespace Philly\Container;

use Philly\Contracts\Container\Container as ContainerContract;
use Philly\Contracts\Container\ContainerIterator as ContainerIteratorContract;
use Philly\Support\Storage;

/**
 * Class Container
 */
class Container extends Storage implements ContainerContract
{
    /**
     * Container constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct([]);

        foreach ($items as $key => $item) {
            $this->offsetSet($key, $item);
        }
    }

    /**
     * @inheritDoc
     */
    public function getKeys(): array
    {
        return array_keys($this->storage);
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        return array_values($this->storage);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->storage);
    }

    /**
     * @inheritDoc
     * @throws OffsetNotFoundException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new OffsetNotFoundException($offset);
        }

        return $this->storage[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->acceptsKey($offset)) {
            if (is_object($offset)) {
                $type = get_class($offset);
            } else {
                $type = gettype($offset);
            }

            throw new UnacceptableKeyException($type);
        }

        if (!$this->accepts($value)) {
            if (is_object($value)) {
                $type = get_class($value);
            } else {
                $type = gettype($value);
            }

            throw new UnacceptableTypeException(type: $type);
        }

        if ($offset !== null) {
            $this->storage[$offset] = $value;
        } else {
            $this->storage[] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->storage[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): ContainerIteratorContract
    {
        return new ContainerIterator($this);
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return $this->offsetExists($id);
    }

    /**
     * @inheritDoc
     */
    public function put($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function acceptsKey($offset): bool
    {
        return $offset === null || is_string($offset) || is_numeric($offset);
    }

    /**
     * @inheritDoc
     */
    public function getLazy($key, $default)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        $this->offsetSet($key, $default);

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function copy(bool $deep = true): ContainerContract
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
