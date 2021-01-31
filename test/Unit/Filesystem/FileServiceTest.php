<?php

declare(strict_types=1);

namespace Filesystem;

use InvalidArgumentException;
use Philly\Container\UnacceptableTypeException;
use Philly\Contracts\Filesystem\Filesystem as FilesystemContract;
use Philly\Contracts\Filesystem\FilesystemContainer as FilesystemContainerContract;
use Philly\Exceptions\NullReferenceException;
use Philly\Filesystem\FilesService;
use Philly\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use test\Philly\TestClass;

/**
 * TODO: remove @psalm-suppress from methods when vimeo/psalm#3357 gets fixed.
 *
 * @psalm-suppress PossiblyNullReference
 */
class FileServiceTest extends TestCase
{
    private ?FilesService $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new FilesService();
        $this->files->onRegistered();
        $this->files->onBooted();
    }

    protected function tearDown(): void
    {
        $this->files = null;
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testGetAll()
    {
        $all = $this->files->getAll();

        static::assertTrue($all->count() > 0);
        static::assertInstanceOf(FilesystemContainerContract::class, $all);
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testGetAllReturnsUniqueInstances()
    {
        $all1 = $this->files->getAll();
        $all2 = $this->files->getAll();

        static::assertNotSame($all1, $all2);
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testGet()
    {
        $root = $this->files->get('philly-root');
        $root2 = $this->files['philly-root'];

        static::assertInstanceOf(FilesystemContract::class, $root);
        static::assertSame($root, $root2);
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testAdd()
    {
        $this->files->add("test", sys_get_temp_dir());
        $this->files->add(new Filesystem("test3", sys_get_temp_dir()));
        $this->files["test4"] = new Filesystem("test3", sys_get_temp_dir());

        static::assertTrue($this->files->getAll()->count() >= 3);
        static::assertInstanceOf(FilesystemContract::class, $this->files["test"]);
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testNoRootAdd()
    {
        static::expectException(NullReferenceException::class);

        $this->files->add("invalid");
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testFsAndRootAdd()
    {
        static::expectException(InvalidArgumentException::class);

        $this->files->add(new Filesystem("invalid", sys_get_temp_dir()), "C:\\");
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testInvalidType()
    {
        static::expectException(UnacceptableTypeException::class);

        $this->files["invalid"] = new TestClass();
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testOffsetExists()
    {
        static::assertTrue($this->files->offsetExists('philly-root'));
        static::assertFalse($this->files->offsetExists('invalid'));
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testOffsetUnset()
    {
        $this->files->add("test", sys_get_temp_dir());

        static::assertTrue($this->files->offsetExists("test"));

        $this->files->offsetUnset("test");

        static::assertFalse($this->files->offsetExists("test"));
    }
}
