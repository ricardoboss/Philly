<?php

declare(strict_types=1);

namespace test\Philly\Unit\ServiceProvider;

use Philly\Container\UnacceptableBindingException;
use Philly\ServiceProvider\AlreadyBootedException;
use Philly\ServiceProvider\ServiceProviderContainer;
use PHPUnit\Framework\TestCase;
use test\Philly\TestClass;
use test\Philly\TestInterface;
use test\Philly\TestServiceProvider;

class ServiceProviderContainerTest extends TestCase
{
    public function testAcceptsNative()
    {
        $container = new ServiceProviderContainer();

        static::expectException(UnacceptableBindingException::class);

        $container->offsetSet(TestInterface::class, "test");
    }

    public function testAcceptsObject()
    {
        $container = new ServiceProviderContainer();

        static::expectException(UnacceptableBindingException::class);

        $container->offsetSet(TestInterface::class, new TestClass());
    }

    public function testAcceptsServiceProvider()
    {
        $container = new ServiceProviderContainer();
        $serviceProvider = new TestServiceProvider();

        $container->offsetSet(TestInterface::class, $serviceProvider);

        $service = $container[TestInterface::class];

        static::assertInstanceOf(TestServiceProvider::class, $service);
    }

    public function testBoot()
    {
        $container = new ServiceProviderContainer();
        $serviceProvider = new TestServiceProvider();

        static::assertFalse($container->isBooted());
        static::assertFalse($serviceProvider->isRegistered());
        static::assertFalse($serviceProvider->isBooted());

        $container[TestInterface::class] = $serviceProvider;

        static::assertFalse($container->isBooted());
        static::assertTrue($serviceProvider->isRegistered());
        static::assertFalse($serviceProvider->isBooted());

        $container->boot();

        static::assertTrue($container->isBooted());
        static::assertTrue($serviceProvider->isRegistered());
        static::assertTrue($serviceProvider->isBooted());

        static::expectException(AlreadyBootedException::class);

        $container->boot();
    }

    public function testBindAfterBoot()
    {
        $container = new ServiceProviderContainer();
        $container->boot();

        static::expectException(AlreadyBootedException::class);

        $container[TestInterface::class] = new TestServiceProvider();
    }
}
