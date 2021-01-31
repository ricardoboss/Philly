<?php

declare(strict_types=1);

namespace Philly\Contracts\Stack;

use IteratorAggregate;
use Philly\Contracts\Support\Storage;
use Philly\Stack\StackEmptyException;
use Traversable;

/**
 * Interface Stack.
 */
interface Stack extends Traversable, IteratorAggregate, Storage
{
    /**
     * @param mixed|mixed[] $value The value(s) to push onto the stack. The first parameter gets pushed first, then the
     * second one and so on...
     */
    public function push(...$value): void;

    /**
     * @return mixed The value on top of the stack. This value also removes the value from the stack.
     *
     * @throws StackEmptyException If the stack is empty.
     */
    public function pop();

    /**
     * @return mixed The value on top of the stack without removing it.
     *
     * @throws StackEmptyException If the stack is empty.
     */
    public function peek();

    /**
     * @inheritDoc
     */
    public function getIterator(): StackIterator;

    /**
     * Copies this stack.
     *
     * @param bool $deep Whether to do a deep copy (also clones every single elements).
     * @return Stack A new stack instance containing similar elements as the original.
     */
    public function copy(bool $deep = true): self;
}
