<?php

declare(strict_types=1);

namespace Philly\Contracts\Filesystem;

use Philly\Contracts\Container\Container;

/**
 * Interface FilesystemContainer.
 */
interface FilesystemContainer extends Container
{
    /**
     * @inheritDoc
     */
    public function copy(bool $deep = true): self;
}
