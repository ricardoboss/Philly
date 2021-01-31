<?php

declare(strict_types=1);

namespace test\Philly\Unit\CLI\Commands;

use InvalidArgumentException;
use Philly\CLI\Commands\CommandArgument;
use Philly\CLI\Commands\CommandArgumentCollection;
use Philly\CLI\Commands\CommandArgumentTemplate;
use Philly\Container\OffsetNotFoundException;
use Philly\Container\UnacceptableTypeException;
use PHPUnit\Framework\TestCase;
use test\Philly\TestCommand;

class CommandArgumentCollectionTest extends TestCase
{
    public function testConstruct()
    {
        $template = new CommandArgumentTemplate("test");
        $collection = new CommandArgumentCollection([
            new CommandArgument($template, "val")
        ]);

        static::assertCount(1, $collection);

        static::expectException(UnacceptableTypeException::class);
        $collection->add("test");
    }

    public function testFromArray()
    {
        $s = TestCommand::makeSignature();
        $collection = CommandArgumentCollection::fromArray(
            $s->getArguments(),
            [
                'invalid' => null,
                'f' => true,
                'val' => null
            ]
        );

        static::assertCount(2, $collection);
    }

    public function testInvalidFromArray()
    {
        $s = TestCommand::makeSignature();

        static::expectException(InvalidArgumentException::class);

        CommandArgumentCollection::fromArray(
            $s->getArguments(),
            [
                'invalid' => null,
            ]
        );
    }

    public function testGetValueInvalidKey()
    {
        $collection = new CommandArgumentCollection();

        static::expectException(OffsetNotFoundException::class);

        $collection->getValue('invalid');
    }
}
