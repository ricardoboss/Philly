<?php

declare(strict_types=1);

namespace Philly\ServiceProvider;

use JetBrains\PhpStorm\Pure;
use Philly\Contracts\ServiceProvider\ServiceProvider as ServiceProviderContract;
use RuntimeException;
use Throwable;

/**
 * Class AlreadyBootedException.
 */
class AlreadyBootedException extends RuntimeException
{
    /**
     * AlreadyBootedException constructor.
     *
     * @param string|ServiceProviderContract $serviceProvider The message or the contract which was already booted.
     */
    #[Pure] public function __construct(string|ServiceProviderContract $serviceProvider, int $code = 0, Throwable $previous = null)
    {
        if ($serviceProvider instanceof ServiceProviderContract) {
            $name = get_class($serviceProvider);

            $message = "Service provider \"{$name}\" was already booted!";
        } else {
            $message = $serviceProvider;
        }

        parent::__construct($message, $code, $previous);
    }
}
