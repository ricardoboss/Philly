<?php

declare(strict_types=1);

namespace Philly\CLI\Commands;

use InvalidArgumentException;
use Philly\Container\OffsetNotFoundException;
use Philly\Contracts\CLI\Commands\CommandArgument as CommandArgumentContract;
use Philly\Contracts\CLI\Commands\CommandArgumentCollection as CommandArgumentCollectionContract;
use Philly\Contracts\CLI\Commands\CommandArgumentTemplate as CommandArgumentTemplateContract;
use Philly\Contracts\CLI\Commands\CommandArgumentTemplateCollection as CommandArgumentTemplateCollectionContract;

class CommandArgumentCollection extends CommandArgumentTemplateCollection implements CommandArgumentCollectionContract
{
    public static function fromArray(CommandArgumentTemplateCollectionContract $argumentTemplateCollection, array $args = []): CommandArgumentCollectionContract
    {
        $collection = new CommandArgumentCollection();

        /** @var CommandArgumentTemplateContract $template */
        foreach ($argumentTemplateCollection as $template) {
            if (array_key_exists($template->getName(), $args)) {
                $value = $args[$template->getName()];
            } elseif (array_key_exists($template->getShortName(), $args)) {
                $value = $args[$template->getShortName()];
            } elseif ($template->isOptional()) {
                $value = $template->getDefaultValue();
            } else {
                throw new InvalidArgumentException("Argument {$template->getName()} is required but no value was supplied.");
            }

            $arg = new CommandArgument($template, $value);
            $collection->add($arg);
        }

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        return $value instanceof CommandArgumentContract;
    }

    /**
     * @inheritDoc
     */
    public function getValue(string $key)
    {
        /** @var CommandArgumentContract $arg */
        $arg = parent::firstKey($key);

        if ($arg === null) {
            throw new OffsetNotFoundException($key);
        }

        return $arg->getValue();
    }
}
