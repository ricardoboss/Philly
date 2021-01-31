<?php

declare(strict_types=1);

namespace test\Philly\Unit\Stack;

use Philly\Stack\Stack;
use Philly\Stack\StackEmptyException;
use PHPUnit\Framework\TestCase;
use test\Philly\TestClass;

class StackTest extends TestCase
{
    public function testJsonSerialize()
    {
        $instance = new TestClass();
        $stack = new Stack();
        $stack->push($instance);

        $json_enc = json_encode($stack, $stack->jsonOptions());
        $json_as = $stack->asJson();

        static::assertEquals("[{\"field\":\"value\"}]", $json_enc);
        static::assertEquals($json_enc, $json_as);
    }

    public function testPush()
    {
        $stack = new Stack();
        $instance = new TestClass();

        $stack->push($instance);

        static::assertCount(1, $stack);
    }

    public function testPop()
    {
        $stack = new Stack();
        $instance = new TestClass();

        $stack->push($instance);

        static::assertCount(1, $stack);

        $result = $stack->pop();

        static::assertSame($instance, $result);
        static::assertCount(0, $stack);

        static::expectException(StackEmptyException::class);
        $stack->pop();
    }

    public function testPeek()
    {
        $stack = new Stack();
        $instance = new TestClass();

        $stack->push($instance);

        static::assertCount(1, $stack);

        $result = $stack->peek();

        static::assertSame($instance, $result);
        static::assertCount(1, $stack);

        $stack->pop();

        static::expectException(StackEmptyException::class);
        $stack->peek();
    }

    public function testIsEmptyAndClear()
    {
        $stack = new Stack();
        $instance = new TestClass();

        $stack->push($instance);

        static::assertFalse($stack->isEmpty());

        $stack->pop();

        static::assertTrue($stack->isEmpty());

        $stack->push($instance);
        $stack->clear();

        static::assertTrue($stack->isEmpty());
    }

    public function testCopy()
    {
        $stack = new Stack();
        $a = new TestClass();
        $a->field = "a";
        $b = new TestClass();
        $b->field = "b";
        $c = new TestClass();
        $c->field = "c";

        $stack->push($c, $b, $a);

        $deep_copy = $stack->copy();

        static::assertNotSame($stack, $deep_copy);

        $o_a = $stack->pop();
        $c_a = $deep_copy->pop();

        static::assertSame($o_a, $a);
        static::assertNotSame($o_a, $c_a);

        $shallow_copy = $stack->copy(false);

        static::assertNotSame($stack, $shallow_copy);

        $o_b = $stack->pop();
        $c_b = $shallow_copy->pop();

        static::assertSame($o_b, $b);
        static::assertSame($o_b, $c_b);
    }
}
