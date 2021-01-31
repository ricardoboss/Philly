<?php

declare(strict_types=1);

namespace test\Philly\Unit\Container;

use Philly\Container\OffsetNotFoundException;
use Philly\Container\UnacceptableKeyException;
use Philly\Container\UnacceptableTypeException;
use PHPUnit\Framework\TestCase;
use stdClass;
use test\Philly\TestClass;
use test\Philly\TestContainer;
use test\Philly\TestInterface;

/**
 * Class ContainerTest.
 */
class ContainerTest extends TestCase
{
    public function testGetValues()
    {
        $instance = new TestClass();

        $container = new TestContainer();
        $container[TestInterface::class] = $instance;

        $values = $container->getValues();

        static::assertIsArray($values);
        static::assertContains($instance, $values);
    }

    public function testGetKeys()
    {
        $key = TestInterface::class;

        $container = new TestContainer();
        $container[$key] = new TestClass();

        $keys = $container->getKeys();

        static::assertIsArray($keys);
        static::assertContainsEquals($key, $keys);
    }

    public function testOffsetSet()
    {
        $instance = new TestClass();
        $container = new TestContainer();
        $container->offsetSet(TestInterface::class, $instance);

        $got = $container[TestInterface::class];

        static::assertTrue($instance === $got);
    }

    public function testOffsetSetNative()
    {
        $container = new TestContainer();

        static::expectException(UnacceptableTypeException::class);

        $container->offsetSet(TestInterface::class, "test");
    }

    public function testOffsetGet()
    {
        $instance = new TestClass();
        $container = new TestContainer();
        $container[TestInterface::class] = $instance;

        $got = $container->offsetGet(TestInterface::class);

        static::assertTrue($instance === $got);
    }

    public function testOffsetUnset()
    {
        $container = new TestContainer();
        $container[TestInterface::class] = new TestClass();

        $container->offsetUnset(TestInterface::class);

        static::expectException(OffsetNotFoundException::class);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $got = $container[TestInterface::class];
    }

    public function testAccepts()
    {
        $instance = new TestClass();
        $container = new TestContainer();
        $container[TestInterface::class] = $instance;

        static::assertContains($instance, $container);

        static::expectException(UnacceptableTypeException::class);
        $container[stdClass::class] = new stdClass();
    }

    public function testJsonSerialize()
    {
        $instance = new TestClass();
        $container = new TestContainer();
        $container[TestInterface::class] = $instance;

        $json_enc = json_encode($container, $container->jsonOptions());
        $json_as = $container->asJson();

        static::assertEquals("{\"test\\\\Philly\\\\TestInterface\":{\"field\":\"value\"}}", $json_enc);
        static::assertEquals($json_enc, $json_as);
    }

    public function testGet()
    {
        $instance = new TestClass();
        $container = new TestContainer();
        $container[TestInterface::class] = $instance;

        $got = $container->get(TestInterface::class);

        static::assertTrue($instance === $got);
    }

    public function testHas()
    {
        $container = new TestContainer();
        $container[TestInterface::class] = new TestClass();

        static::assertTrue($container->has(TestInterface::class));
    }

    public function testOffsetExists()
    {
        $container = new TestContainer();
        $container[TestInterface::class] = new TestClass();

        static::assertTrue($container->offsetExists(TestInterface::class));
    }

    public function testPut()
    {
        $instance = new TestClass();
        $container = new TestContainer();
        $container->put(TestInterface::class, $instance);

        $got = $container[TestInterface::class];

        static::assertTrue($instance === $got);
    }

    public function testGetLazy()
    {
        $container = new TestContainer();
        $instance = new TestClass();

        $default = $container->getLazy("key", $instance);

        static::assertSame($instance, $default);

        $lazy = $container->getLazy("key", new TestClass());

        static::assertSame($instance, $lazy);
    }

    public function testUnacceptableKeyObject()
    {
        $container = new TestContainer();

        static::expectException(UnacceptableKeyException::class);

        $container->offsetSet(new TestClass(), new TestClass());
    }

    public function testUnacceptableKeyNative()
    {
        $container = new TestContainer();

        static::expectException(UnacceptableKeyException::class);

        $container->offsetSet(true, new TestClass());
    }

    public function testClear()
    {
        $container = new TestContainer([new TestClass()]);

        static::assertCount(1, $container);

        $container->clear();

        static::assertCount(0, $container);
    }

    public function testIsEmpty()
    {
        $container = new TestContainer();

        static::assertTrue($container->isEmpty());

        $container[TestInterface::class] = new TestClass();

        static::assertFalse($container->isEmpty());

        $container->clear();

        static::assertTrue($container->isEmpty());
    }

    public function testCopy()
    {
        $a = new TestClass();

        $container = new TestContainer([
            TestInterface::class => $a
        ]);

        $deep_copy = $container->copy();
        $deep_a = $deep_copy[TestInterface::class];

        static::assertNotSame($a, $deep_a);

        $shallow_copy = $container->copy(false);
        $shallow_a = $shallow_copy[TestInterface::class];

        static::assertSame($a, $shallow_a);
    }
}
