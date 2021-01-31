<?php

declare(strict_types=1);

namespace Filesystem;

use InvalidArgumentException;
use Philly\Filesystem\FilesystemContainer;
use PHPUnit\Framework\TestCase;

class FilesystemContainerTest extends TestCase
{
    public function testNoShallowCopy()
    {
        $c = new FilesystemContainer();

        static::expectException(InvalidArgumentException::class);

        $c->copy(deep: false);
    }
}
