<?php

declare(strict_types=1);

namespace Philly;

use InvalidArgumentException;
use Philly\CLI\Commands\CommandCollection;
use Philly\Container\BindingContainer;
use Philly\Container\UnacceptableTypeException;
use Philly\Contracts\App as AppContract;
use Philly\Contracts\CLI\Commands\CommandCollection as CommandCollectionContract;
use Philly\Contracts\Exceptions\ExceptionHandler as ExceptionHandlerContract;
use Philly\Contracts\Filesystem\FilesService as FilesServiceContract;
use Philly\Contracts\ServiceProvider\ServiceProviderContainer as ServiceProviderContainerContract;
use Philly\Exceptions\ExceptionHandler;
use Philly\Filesystem\FilesService;
use Philly\ServiceProvider\ServiceProviderContainer;

/**
 * Class App
 */
class App extends BindingContainer implements AppContract
{
    public const VERSION = "0.0.3";

    protected static ?AppContract $instance = null;

    /**
     * @return AppContract The global app instance.
     */
    public static function inst(): AppContract
    {
        if (static::$instance === null) {
            static::$instance = new App();
        }

        return static::$instance;
    }

    /**
     * App constructor.
     */
    private function __construct()
    {
        parent::__construct();

        // bind this app instance to its own class
        $this[AppContract::class] = $this;
    }

    /**
     * @inheritDoc
     */
    public function getExceptionHandler(): ExceptionHandlerContract
    {
        $handler = $this->getLazySingleton(
            ExceptionHandlerContract::class,
            fn () => new ExceptionHandler()
        );

        if (!($handler instanceof ExceptionHandlerContract)) {
            throw new UnacceptableTypeException("Invalid exception handler type!");
        }

        return $handler;
    }

    /**
     * @inheritDoc
     */
    public function getServices(): ServiceProviderContainerContract
    {
        $serviceContainer = $this->getLazySingleton(
            ServiceProviderContainerContract::class,
            function () {
                $serviceContainer = new ServiceProviderContainer();

                // register base services
                $serviceContainer[FilesServiceContract::class] = new FilesService();

                return $serviceContainer;
            }
        );

        if (!($serviceContainer instanceof ServiceProviderContainerContract)) {
            throw new UnacceptableTypeException(sprintf("Invalid service provider container type: %s!", get_class($serviceContainer)));
        }

        return $serviceContainer;
    }

    /**
     * @inheritDoc
     */
    public function getCommands(): CommandCollectionContract
    {
        if (!$this->offsetExists(CommandCollectionContract::class)) {
            $this[CommandCollectionContract::class] = new CommandCollection();
        }

        $commandCollection = $this->get(CommandCollectionContract::class);

        if (!($commandCollection instanceof CommandCollectionContract)) {
            throw new UnacceptableTypeException("Invalid command collection type!");
        }

        return $commandCollection;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return parent::offsetGet($offset);
        }

        if ($this->getServices()->offsetExists($offset)) {
            return $this->getServices()->offsetGet($offset);
        }

        throw new InvalidArgumentException("Service of type $offset not found in app container.");
    }
}
