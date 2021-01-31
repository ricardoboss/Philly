<?php

declare(strict_types=1);

namespace Philly\Filesystem;

use Philly\Contracts\Filesystem\Filesystem as FilesystemContract;
use Philly\Support\Str;
use ricardoboss\Console;

class Filesystem implements FilesystemContract
{
    protected string $name;
    protected string $root;

    /**
     * Filesystem constructor.
     *
     * @param string $root The root of the filesystem.
     */
    public function __construct(string $name, string $root)
    {
        $this->name = $name;
        $this->root = Str::finish($root, DIRECTORY_SEPARATOR);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @inheritDoc
     */
    public function exists(string $path): bool
    {
        return is_file($this->root . $path);
    }

    /**
     * @inheritDoc
     */
    public function getContents(string $path): string
    {
        if (!$this->exists($path)) {
            throw new FileNotFoundException(path: $this->root . $path);
        }

        return file_get_contents($this->root . $path);
    }

    /**
     * @inheritDoc
     */
    public function putContents(string $path, string $content, bool $overwrite = true): bool
    {
        if ($this->exists($path) && !$overwrite) {
            throw new FileExistsException(path: $this->root . $path);
        } elseif ($overwrite) {
            $this->makeDirs($path);
        }

        $full = $this->root . $path;

        $h = @fopen($full, "w");
        if ($h === false) {
            throw new FileNotCreatedException(path: $full);
        }

        $result = fwrite($h, $content);
        fclose($h);

        if ($result) {
            Console::debug("Wrote new content to %s", Console::link($full));

            return true;
        }

        return false;
    }

    /**
     * @param string $path
     * @return bool
     * @throws FileNotCreatedException
     */
    private function makeDirs(string $path, bool $throw = true): bool
    {
        $dirs = dirname($this->root . $path);

        if (is_dir($dirs)) {
            return true;
        }

        $success = @mkdir($dirs, recursive: true);
        if (!$success && $throw) {
            throw new FileNotCreatedException("Unable to create directories: $dirs");
        }

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function real(string $path, bool $throw = true): ?string
    {
        $real = realpath($this->root . $path);

        if ($real === false && $throw) {
            throw new FileNotFoundException(path: $path);
        } elseif (!$throw) {
            return null;
        }

        return $real;
    }
}
