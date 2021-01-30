<?php

declare(strict_types=1);

namespace test\Philly\Unit\Foundation\CLI\Commands;

use Philly\CLI\Commands\Command;
use Philly\CLI\Commands\CommandArgumentCollection;
use Philly\Exceptions\NullReferenceException;
use Philly\Filesystem\FileNotCreatedException;
use Philly\Filesystem\FileNotFoundException;
use Philly\Foundation\CLI\Commands\CreateCommandCommand;
use PHPUnit\Framework\TestCase;

class CreateCommandCommandTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        $tmp = sys_get_temp_dir();

        $tmpFiles = glob($tmp . DIRECTORY_SEPARATOR . "*.php");
        if ($tmpFiles === false)
            return;

        foreach ($tmpFiles as $tmpFile)
            unlink($tmpFile);
    }

    public function testConstruct()
    {
        $cmd = new CreateCommandCommand();

        static::assertEquals("create:command", $cmd->getSignature()->getName());
    }

    public function testHandleRandomName()
    {
        $cmd = new CreateCommandCommand();
        $tmp = sys_get_temp_dir();
        $ns = "App1\\test2\\Commands3";

        /** @noinspection PhpUnhandledExceptionInspection */
        $rand = random_int(0, 99);

        $r = $cmd->handle(
            CommandArgumentCollection::fromArray(
                $cmd->getSignature()->getArguments(),
                [
                    'name' => "test$rand",
                    'short' => "t",
                    'args' => "arg1,arg2,arg3",
                    'namespace' => $ns,
                    'dest' => $tmp
                ]
            )
        );

        static::assertTrue($r->isSuccess());
        static::assertNull($r->getThrowable());
        static::assertIsString($r->getValue());

        /** @var string $fileLoc */
        $fileLoc = $r->getValue();
        static::assertTrue(file_exists($fileLoc));

        /** @noinspection PhpIncludeInspection */
        include $fileLoc;

        $classname = $ns . "\\Test{$rand}Command";
        static::assertTrue(class_exists($classname));

        /** @var Command $c */
        $c = new $classname();
        static::assertInstanceOf(Command::class, $c);

        $genSig = $c->getSignature();
        static::assertEquals("test$rand", $genSig->getName());
    }

    public function testHandleCustomClassname()
    {
        $cmd = new CreateCommandCommand();
        $tmp = sys_get_temp_dir();
        $ns = "App1\\test2\\Commands3";
        /** @noinspection PhpUnhandledExceptionInspection */
        $rand = random_int(0, 99);
        $cn = "Other{$rand}Command";

        $r = $cmd->handle(
            CommandArgumentCollection::fromArray(
                $cmd->getSignature()->getArguments(),
                [
                    'name' => "test$rand",
                    'short' => "t",
                    'args' => "arg1,arg2,arg3",
                    'namespace' => $ns,
                    'dest' => $tmp,
                    'classname' => $cn
                ]
            )
        );

        static::assertTrue($r->isSuccess());
        static::assertNull($r->getThrowable());
        static::assertIsString($r->getValue());

        /** @var string $fileLoc */
        $fileLoc = $r->getValue();
        static::assertTrue(file_exists($fileLoc));

        /** @noinspection PhpIncludeInspection */
        include $fileLoc;

        $classname = $ns . "\\" . $cn;
        static::assertTrue(class_exists($classname));
    }

    public function testHandleInvalidStub()
    {
        $cmd = new CreateCommandCommand();
        $tmp = sys_get_temp_dir();

        /** @noinspection PhpUnhandledExceptionInspection */
        $rand = random_int(0, 99);

        $r = $cmd->handle(
            CommandArgumentCollection::fromArray(
                $cmd->getSignature()->getArguments(),
                [
                    'name' => "test$rand",
                    'dest' => $tmp,
                    'stub' => "$tmp/I_DON_T_EXIST"
                ]
            )
        );

        static::assertFalse($r->isSuccess());
        static::assertInstanceOf(FileNotFoundException::class, $r->getThrowable());
        static::assertNull($r->getValue());
    }

    public function testHandleNoStub()
    {
        $cmd = new CreateCommandCommand();
        $tmp = sys_get_temp_dir();

        /** @noinspection PhpUnhandledExceptionInspection */
        $rand = random_int(0, 99);

        $r = $cmd->handle(
            CommandArgumentCollection::fromArray(
                $cmd->getSignature()->getArguments(),
                [
                    'name' => "test$rand",
                    'dest' => $tmp,
                    'stub' => ''
                ]
            )
        );

        static::assertFalse($r->isSuccess());
        static::assertInstanceOf(NullReferenceException::class, $r->getThrowable());
        static::assertNull($r->getValue());
    }

    public function testHandleEmptyArgs()
    {
        $cmd = new CreateCommandCommand();
        $tmp = sys_get_temp_dir();
        $ns = "App1\\test2\\Commands3";
        /** @noinspection PhpUnhandledExceptionInspection */
        $rand = random_int(0, 99);
        $cn = "Other{$rand}Command";

        $r = $cmd->handle(
            CommandArgumentCollection::fromArray(
                $cmd->getSignature()->getArguments(),
                [
                    'name' => "test$rand",
                    'short' => "t",
                    'args' => ",,,arg1",
                    'namespace' => $ns,
                    'dest' => $tmp,
                    'classname' => $cn
                ]
            )
        );

        static::assertTrue($r->isSuccess());

        /** @var string $fileLoc */
        $fileLoc = $r->getValue();

        /** @noinspection PhpIncludeInspection */
        include $fileLoc;

        $classname = $ns . "\\" . $cn;
        /** @var Command $c */
        $c = new $classname();
        $genSig = $c->getSignature();
        static::assertCount(1, $genSig->getArguments());
    }

    public function testHandleInvalidDestination()
    {
        $cmd = new CreateCommandCommand();

        /** @noinspection PhpUnhandledExceptionInspection */
        $rand = random_int(0, 99);

        $r = $cmd->handle(
            CommandArgumentCollection::fromArray(
                $cmd->getSignature()->getArguments(),
                [
                    'name' => "test$rand",
                    'dest' => "",
                ]
            )
        );

        static::assertFalse($r->isSuccess());
        static::assertInstanceOf(FileNotCreatedException::class, $r->getThrowable());
        static::assertNull($r->getValue());
    }
}
