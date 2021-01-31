<?php

declare(strict_types=1);

namespace Philly\Stack;

use Philly\Contracts\Stack\Stack as StackContract;
use Philly\Contracts\Stack\StackIterator as StackIteratorContract;
use Philly\Support\JsonCompatible;
use UnderflowException;

class Stack implements StackContract
{
    use JsonCompatible;

    /** @var \Ds\Stack $stack The Data Structures implementation of a stack. */
    protected \Ds\Stack $stack;

    /**
     * Stack constructor.
     */
    public function __construct(array $items = [])
    {
        $this->stack = new \Ds\Stack($items);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): StackIteratorContract
    {
        return new StackIterator($this);
    }

    /**
     * @inheritDoc
     */
    public function push(...$value): void
    {
        $this->stack->push(...$value);
    }

    /**
     * @inheritDoc
     */
    public function pop()
    {
        try {
            return $this->stack->pop();
        } catch (UnderflowException $ue) {
            throw new StackEmptyException($ue->getMessage(), $ue->getCode(), $ue);
        }
    }

    /**
     * @inheritDoc
     */
    public function peek()
    {
        try {
            return $this->stack->peek();
        } catch (UnderflowException $ue) {
            throw new StackEmptyException($ue->getMessage(), $ue->getCode(), $ue);
        }
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return $this->stack->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->stack->count();
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->stack->clear();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->stack->jsonSerialize();
    }

    /**
     * @inheritDoc
     */
    public function copy(bool $deep = true): self
    {
        if (!$deep) {
            return new self(array_reverse($this->stack->toArray()));
        }

        $copy = new self();
        foreach (array_reverse($this->stack->toArray()) as $item) {
            $copy->stack[] = clone $item;
        }

        return $copy;
    }
}
