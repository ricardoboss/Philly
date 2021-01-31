<?php

declare(strict_types=1);

namespace test\Philly\Unit;

use InvalidArgumentException;
use Philly\Container\UnacceptableTypeException;
use Philly\Contracts\CLI\Commands\CommandCollection as CommandCollectionContract;
use Philly\Contracts\Exceptions\ExceptionHandler as ExceptionHandlerContract;
use Philly\Contracts\ServiceProvider\ServiceProviderContainer as ServiceProviderContainerContract;
use PHPUnit\Framework\TestCase;
use test\Philly\TestApp;
use test\Philly\TestClass;
use test\Philly\TestServiceProvider;

class AppTest extends TestCase
{
    public function testGetExceptionHandler()
    {
        try {
            TestApp::reset();
            $app = TestApp::inst();

            $handler = $app->getExceptionHandler();

            static::assertInstanceOf(ExceptionHandlerContract::class, $handler);

            $handler2 = $app->getExceptionHandler();

            static::assertSame($handler, $handler2);
        } finally {
            TestApp::reset();
        }
    }

    public function testGetServices()
    {
        try {
            TestApp::reset();
            $app = TestApp::inst();

            $services = $app->getServices();

            static::assertInstanceOf(ServiceProviderContainerContract::class, $services);

            $services2 = $app->getServices();

            static::assertSame($services, $services2);
        } finally {
            TestApp::reset();
        }
    }

    public function testGetCommands()
    {
        try {
            TestApp::reset();
            $app = TestApp::inst();

            $commands = $app->getCommands();

            static::assertInstanceOf(CommandCollectionContract::class, $commands);

            $commands2 = $app->getCommands();

            static::assertSame($commands, $commands2);
            static::assertGreaterThan(1, count($commands));
        } finally {
            TestApp::reset();
        }
    }

    public function testGetInvalidExceptionHandler()
    {
        try {
            TestApp::reset();
            $app = TestApp::inst();

            $app->bind(ExceptionHandlerContract::class, fn () => new TestClass(), true);

            static::expectException(UnacceptableTypeException::class);

            $app->getExceptionHandler();
        } finally {
            TestApp::reset();
        }
    }

    public function testGetInvalidServices()
    {
        try {
            TestApp::reset();
            $app = TestApp::inst();

            $app->bind(ServiceProviderContainerContract::class, fn () => new TestClass(), true);

            static::expectException(UnacceptableTypeException::class);

            $app->getServices();
        } finally {
            TestApp::reset();
        }
    }

    public function testGetInvalidCommands()
    {
        try {
            TestApp::reset();
            $app = TestApp::inst();

            $app->bind(CommandCollectionContract::class, fn () => new TestClass(), true);

            static::expectException(UnacceptableTypeException::class);

            $app->getCommands();
        } finally {
            TestApp::reset();
        }
    }

    public function testCombinedOffsetGet()
    {
        try {
            TestApp::reset();
            $app = TestApp::inst();

            // register service provider in services
            $app->getServices()->bind(TestServiceProvider::class, fn () => new TestServiceProvider());

            // use app to access app services as well as the service container
            $appService = $app[ServiceProviderContainerContract::class];
            $spService = $app[TestServiceProvider::class];

            static::assertInstanceOf(ServiceProviderContainerContract::class, $appService);
            static::assertInstanceOf(TestServiceProvider::class, $spService);

            static::expectException(InvalidArgumentException::class);

            // not bound service
            $app[TestClass::class];
        } finally {
            TestApp::reset();
        }
    }
}
