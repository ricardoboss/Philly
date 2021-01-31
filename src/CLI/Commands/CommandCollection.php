<?php

declare(strict_types=1);

namespace Philly\CLI\Commands;

use Philly\Container\Collection;
use Philly\Contracts\CLI\Commands\Command as CommandContract;
use Philly\Contracts\CLI\Commands\CommandCollection as CommandCollectionContract;
use Philly\Foundation\CLI\Commands\CreateCommandCommand;
use Philly\Foundation\CLI\Commands\VersionCommand;

/**
 * Class CommandCollection.
 */
class CommandCollection extends Collection implements CommandCollectionContract
{
    public function __construct(array $items = [], bool $registerDefaults = true)
    {
        if ($registerDefaults) {
            // prepend the default commands before any other commands
            array_unshift(
                $items,
                new VersionCommand(),
                new CreateCommandCommand()
            );
        }

        parent::__construct($items);
    }

    public function accepts($value): bool
    {
        return $value instanceof CommandContract;
    }
}
