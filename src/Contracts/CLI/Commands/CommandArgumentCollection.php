<?php

declare(strict_types=1);

namespace Philly\Contracts\CLI\Commands;

/**
 * Interface CommandArgumentCollection.
 */
interface CommandArgumentCollection extends CommandArgumentTemplateCollection
{
    /**
     * Gets the value of an argument by the specified key.
     *
     * @param string $key The key of the argument.
     * @return mixed|null The value of the argument or the default value if this argument is optional.
     */
    public function getValue(string $key);
}
