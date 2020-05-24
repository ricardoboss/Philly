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
        if (is_int($offset) && $offset >= $this->nextOffset)
            $this->nextOffset = $offset + 1;

        parent::offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if (is_numeric($offset) && $offset == $this->nextOffset - 1)
            $this->nextOffset = $offset;

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

        return new Collection($items);
    }

    /**
     * @inheritDoc
     */
    public function first(callable $callback)
    {
        foreach ($this as $key => $value) {
            if ($callback($value, $key) === true)
                return $value;
        }

        return null;
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
	public function asArray(): array
	{
		return $this->storage;
	}
}