<?php

declare(strict_types=1);

namespace Philly\Queue;

use Philly\Contracts\Queue\Queue as QueueContract;
use Philly\Contracts\Queue\QueueIterator as QueueIteratorContract;
use Philly\Support\JsonCompatible;
use UnderflowException;

/**
 * Class Queue.
 */
class Queue implements QueueContract
{
    use JsonCompatible;

    /** @var \Ds\Queue The Data Structures implementation of a queue. */
    protected \Ds\Queue $queue;

    /**
     * Queue constructor.
     */
    public function __construct(array $items = [])
    {
        $this->queue = new \Ds\Queue($items);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): QueueIteratorContract
    {
        return new QueueIterator($this);
    }

    /**
     * @inheritDoc
     */
    public function enqueue(...$value): void
    {
        $this->queue->push(...$value);
    }

    /**
     * @inheritDoc
     */
    public function dequeue()
    {
        try {
            return $this->queue->pop();
        } catch (UnderflowException $ue) {
            throw new QueueEmptyException($ue->getMessage(), $ue->getCode(), $ue);
        }
    }

    /**
     * @inheritDoc
     */
    public function head()
    {
        try {
            return $this->queue->peek();
        } catch (UnderflowException $ue) {
            throw new QueueEmptyException($ue->getMessage(), $ue->getCode(), $ue);
        }
    }

    /**
     * @inheritDoc
     */
    public function tail()
    {
        $arr = $this->queue->toArray();
        $value = end($arr);
        if ($value === false) {
            throw new QueueEmptyException();
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->queue->count();
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->queue->clear();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->queue->jsonSerialize();
    }

    /**
     * @inheritDoc
     */
    public function copy(bool $deep = true): self
    {
        if (!$deep) {
            return new self($this->queue->toArray());
        }

        $copy = new self();
        foreach (array_reverse($this->queue->toArray()) as $item) {
            $copy->queue[] = clone $item;
        }

        return $copy;
    }
}
