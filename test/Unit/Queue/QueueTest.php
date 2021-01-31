<?php

declare(strict_types=1);

namespace test\Philly\Unit\Queue;

use Philly\Queue\Queue;
use Philly\Queue\QueueEmptyException;
use PHPUnit\Framework\TestCase;
use test\Philly\SecondTestClass;
use test\Philly\TestClass;

/**
 * Class QueueTest.
 */
class QueueTest extends TestCase
{
    public function testHead()
    {
        $queue = new Queue();

        $instance = new TestClass();
        $queue->enqueue($instance);

        $result = $queue->head();
        static::assertSame($instance, $result);
    }

    public function testDequeue()
    {
        $queue = new Queue();

        $instance = new TestClass();
        $queue->enqueue($instance);

        $result = $queue->dequeue();
        static::assertSame($instance, $result);

        static::expectException(QueueEmptyException::class);
        $queue->dequeue();
    }

    public function testTail()
    {
        $queue = new Queue();

        $instance_a = new TestClass();
        $instance_b = new SecondTestClass();

        $queue->enqueue($instance_a, $instance_b);

        $result = $queue->tail();

        static::assertSame($instance_b, $result);

        $queue->dequeue();
        $queue->dequeue();

        static::expectException(QueueEmptyException::class);
        $queue->tail();
    }

    public function testIsEmpty()
    {
        $queue = new Queue();

        static::assertTrue($queue->isEmpty());
        static::assertCount(0, $queue);

        $queue->enqueue(1);

        static::assertFalse($queue->isEmpty());
        static::assertCount(1, $queue);
    }

    public function testClear()
    {
        $queue = new Queue([1]);

        static::assertCount(1, $queue);

        $queue->clear();

        static::assertCount(0, $queue);
    }

    public function testJsonSerialize()
    {
        $queue = new Queue([1, 2]);

        $json_enc = json_encode($queue, $queue->jsonOptions());
        $json_as = $queue->asJson();

        static::assertEquals("[1,2]", $json_enc);
        static::assertEquals($json_enc, $json_as);
    }

    public function testCopy()
    {
        $queue = new Queue();
        $a = new TestClass();
        $a->field = "a";
        $b = new TestClass();
        $b->field = "b";
        $c = new TestClass();
        $c->field = "c";

        $queue->enqueue($a, $b, $c);

        $deep_copy = $queue->copy();

        static::assertNotSame($queue, $deep_copy);

        $o_a = $queue->dequeue();
        $c_a = $deep_copy->dequeue();

        static::assertSame($o_a, $a);
        static::assertNotSame($o_a, $c_a);

        $shallow_copy = $queue->copy(false);

        static::assertNotSame($queue, $shallow_copy);

        $o_b = $queue->dequeue();
        $c_b = $shallow_copy->dequeue();

        static::assertSame($o_b, $b);
        static::assertSame($o_b, $c_b);
    }
}
