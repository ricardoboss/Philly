<?php

declare(strict_types=1);

namespace Philly\ServiceProvider;

use Philly\Contracts\ServiceProvider\ServiceProvider as ServiceProviderContract;
use ricardoboss\Console;

/**
 * Class ServiceProvider.
 */
abstract class ServiceProvider implements ServiceProviderContract
{
    /** @var bool Whether this service provider was already registered. */
    private bool $registered = false;

    /** @var bool Whether this service provider was already booted. */
    private bool $booted = false;

    /**
     * @inheritDoc
     */
    public function isRegistered(): bool
    {
        return $this->registered;
    }

    /**
     * @inheritDoc
     */
    public function onRegistered(): void
    {
        if ($this->isRegistered()) {
            throw new AlreadyRegisteredException($this);
        }

        $this->registered = true;

        Console::debug("Service provider " . get_class($this) . " registered.");
    }

    /**
     * @inheritDoc
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * @inheritDoc
     */
    public function onBooted(): void
    {
        if (!$this->isRegistered()) {
            throw new NotRegisteredException($this);
        }

        if ($this->isBooted()) {
            throw new AlreadyBootedException($this);
        }

        $this->booted = true;

        Console::debug("Service provider " . get_class($this) . " booted.");
    }
}
