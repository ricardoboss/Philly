<?php

declare(strict_types=1);

namespace Philly\ServiceProvider;

use Philly\Container\BindingContainer;
use Philly\Contracts\Container\BindingContract as BaseBindingContract;
use Philly\Contracts\ServiceProvider\ServiceProvider as ServiceProviderContract;
use Philly\Contracts\ServiceProvider\ServiceProviderContainer as ServiceProviderContainerContract;

/**
 * Class ServiceProviderContainer.
 */
class ServiceProviderContainer extends BindingContainer implements ServiceProviderContainerContract
{
    protected bool $booted = false;
    protected bool $booting = false;

    /**
     * @inheritDoc
     */
    public function acceptsBinding($value): bool
    {
        return $value instanceof ServiceProviderContract;
    }

    public function bind(string $interface, $builder, bool $singleton = true): BaseBindingContract
    {
        if ($this->booted) {
            throw new AlreadyBootedException("Service provider container was already booted!");
        }

        // all services must be singletons
        $contract = parent::bind($interface, $builder, true);

        /** @var ServiceProviderContract $service */
        $service = parent::offsetGet($interface);

        if (!$service->isRegistered()) {
            $service->onRegistered();
        }

        return $contract;
    }

    public function offsetGet($offset)
    {
        if (!$this->booted && !$this->booting) {
            $this->boot();
        }

        return parent::offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if ($this->booted || $this->booting) {
            throw new AlreadyBootedException("Service provider container " . get_class($this) . " was already booted.");
        }

        $this->booting = true;

        /** @var ServiceProviderContract $service */
        foreach ($this as $service) {
            $service->onBooted();
        }

        $this->booting = false;
        $this->booted = true;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }
}
