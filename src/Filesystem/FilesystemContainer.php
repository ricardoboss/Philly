<?php

declare(strict_types=1);

namespace Philly\Filesystem;

use InvalidArgumentException;
use Philly\Container\Container;
use Philly\Contracts\Filesystem\Filesystem as FilesystemContract;
use Philly\Contracts\Filesystem\FilesystemContainer as FilesystemContainerContract;

/**
 * Class FilesystemContainer.
 */
class FilesystemContainer extends Container implements FilesystemContainerContract
{
    /**
     * @inheritDoc
     */
    public function accepts($value): bool
    {
        return $value instanceof FilesystemContract;
    }

    /**
     * @inheritDoc
     */
    public function copy(bool $deep = true): FilesystemContainerContract
    {
        if (!$deep) {
            throw new InvalidArgumentException("Cannot create a shallow copy of a filesystem container! (This would allow modifying existing filesystems in another container)");
        }

        $copy = new self();
        foreach ($this->storage as $k => $v) {
            $copy->storage[$k] = clone $v;
        }

        return $copy;
    }
}
