<?php

declare(strict_types=1);

namespace test\Philly\Unit\Filesystem;

use Philly\Filesystem\FileExistsException;
use Philly\Filesystem\FileNotCreatedException;
use Philly\Filesystem\FileNotFoundException;
use Philly\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    public function testGetName()
    {
        $f = new Filesystem("test", "C:\\www\\test.com");

        static::assertSame("test", $f->getName());
    }

    public function testReal()
    {
        $f = new Filesystem("test", __DIR__);

        static::assertNotNull($f->real(basename(__FILE__)));
    }

    public function testExists()
    {
        $f = new Filesystem("test", __DIR__);

        static::assertTrue($f->exists(basename(__FILE__)));
    }

    public function testContents()
    {
        $root = sys_get_temp_dir();
        $f = new Filesystem("temp", $root);
        $filename = "testfile" . random_int(0, 99);
        $nested_filename = "test/testfile" . random_int(0, 99);

        try {
            static::assertFalse($f->exists($filename));
            static::assertFalse($f->exists($nested_filename));

            static::assertTrue($f->putContents($filename, "This is some random test string"));
            static::assertTrue($f->putContents($nested_filename, "This is some random test string in a nested file"));

            static::assertTrue($f->exists($filename));
            static::assertTrue($f->exists($nested_filename));

            static::assertSame("This is some random test string", $f->getContents($filename));
            static::assertSame("This is some random test string in a nested file", $f->getContents($nested_filename));
        } finally {
            if (file_exists($root . DIRECTORY_SEPARATOR . $filename)) {
                unlink($root . DIRECTORY_SEPARATOR . $filename);
            }

            if (file_exists($root . DIRECTORY_SEPARATOR . $nested_filename)) {
                unlink($root . DIRECTORY_SEPARATOR . $nested_filename);
                rmdir(dirname($root . DIRECTORY_SEPARATOR . $nested_filename));
            }
        }

        static::expectException(FileNotFoundException::class);

        $f->getContents($filename);
    }

    public function testNoOverwriteContents()
    {
        $root = sys_get_temp_dir();
        $f = new Filesystem("temp", $root);
        $filename = "testfile" . random_int(0, 99);

        try {
            static::assertFalse($f->exists($filename));

            static::assertTrue($f->putContents($filename, "This is some random test string"));

            static::assertTrue($f->exists($filename));

            static::expectException(FileExistsException::class);

            $f->putContents($filename, "test", false);
        } finally {
            if (file_exists($root . DIRECTORY_SEPARATOR . $filename)) {
                unlink($root . DIRECTORY_SEPARATOR . $filename);
            }
        }
    }

    public function testNotReal()
    {
        $f = new Filesystem("temp", sys_get_temp_dir());

        static::assertNull($f->real("non-existent-filename", false));

        static::expectException(FileNotFoundException::class);

        $f->real("non-existent-filename");
    }

    public function testGetRoot()
    {
        $f = new Filesystem("test", "C:\\www\\test.com");

        static::assertSame("C:\\www\\test.com" . DIRECTORY_SEPARATOR, $f->getRoot());
    }

    public function testNoFileCreated()
    {
        $f = new Filesystem("temp", sys_get_temp_dir());

        static::expectException(FileNotCreatedException::class);

        $f->putContents("", "test");
    }
}
