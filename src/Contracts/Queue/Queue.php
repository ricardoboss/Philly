<?php

declare(strict_types=1);

namespace Philly\Contracts\Queue;

use IteratorAggregate;
use Philly\Contracts\Support\Storage;
use Traversable;

/**
 * Interface Queue.
 */
interface Queue extends Traversable, IteratorAggregate, Storage
{
    /**
     * Enqueue a new value into the queue.
     *
     * @param mixed|mixed[] $value The value to enqueue.
     */
    public function enqueue(...$value): void;

    /**
     * Dequeue the first value in the queue. This removed the value form the queue.
     *
     * @return mixed The head of the queue.
     */
    public function dequeue();

    /**
     * @return mixed The head of the queue without removing it from the queue.
     */
    public function head();

    /**
     * @return mixed The last element of the queue.
     */
    public function tail();

    /**
     * @inheritDoc
     */
    public function getIterator(): QueueIterator;

    /**
     * Copies this queue.
     *
     * @param bool $deep Whether to do a deep copy (also clones every single elements).
     * @return Queue A new queue instance containing similar elements as the original.
     */
    public function copy(bool $deep = true): self;
}
