<?php

declare(strict_types=1);

namespace Philly\Foundation\CLI\Commands;

use JetBrains\PhpStorm\Pure;
use Philly\App;
use Philly\CLI\Commands\Command;
use Philly\CLI\Commands\CommandArgumentTemplate;
use Philly\CLI\Commands\CommandResult;
use Philly\CLI\Commands\CommandSignature;
use Philly\Container\Collection;
use Philly\Contracts\CLI\Commands\CommandArgumentCollection as CommandArgumentCollectionContract;
use Philly\Contracts\CLI\Commands\CommandResult as CommandResultContract;
use Philly\Contracts\Filesystem\FilesService as FilesServiceContract;
use Philly\Exceptions\NullReferenceException;
use Philly\Filesystem\FileExistsException;
use Philly\Filesystem\FileNotCreatedException;
use Philly\Filesystem\FileNotFoundException;
use Philly\Support\Str;
use ricardoboss\Console;

/**
 * Class CreateCommandCommand.
 */
class CreateCommandCommand extends Command
{
    private FilesServiceContract $files;

    public function __construct()
    {
        $this->files = App::inst()->getServices()->get(FilesServiceContract::class);

        parent::__construct(new CommandSignature(
            "create:command",
            [
                new CommandArgumentTemplate("name"),
                new CommandArgumentTemplate("short", optional: true),
                new CommandArgumentTemplate("args", optional: true),
                new CommandArgumentTemplate("namespace", optional: true, default: "CLI\\Commands"),
                new CommandArgumentTemplate("classname", optional: true),
                new CommandArgumentTemplate("stub", optional: true),
                new CommandArgumentTemplate("dest", optional: true, default: "app/CLI/Commands/")
            ]
        ));
    }

    /**
     * @inheritDoc
     */
    public function handle(CommandArgumentCollectionContract $args): CommandResultContract
    {
        /** @var string $name */
        $name = $args->getValue('name');
        Console::debug("Command name: %s", $name);

        /** @var string $namespace */
        $namespace = $args->getValue('namespace');
        Console::debug("Namespace: %s", $namespace);

        /** @var string[] $arg_names */
        $arg_names = (new Collection(explode(",", $args->getValue("args") ?? "")))
            ->where(fn ($v) => strlen($v) > 0) // filter empty args
            ->asArray();
        Console::debug("Arg names: [%s]", implode(', ', $arg_names));

        /** @var string $classname */
        $classname = $args->getValue("classname") ?? ucfirst(Str::camel($name));
        if (!Str::endsWith($classname, "Command")) {
            $classname .= "Command";
        }
        Console::debug("Classname: %s", $classname);

        /** @var string $stub_path */
        $stub_path = $args->getValue("stub") ?? $this->files["philly-root"]->real("src/Foundation/CLI/Commands/stubs/Command.php.stub");
        Console::debug("Stub path: %s", $stub_path);

        /** @var string $destination */
        $destination = $args->getValue("dest");
        if (strlen($destination) == 0) {
            return CommandResult::fail(new FileNotCreatedException("Invalid destination: cannot be empty"));
        }

        $filename = Str::finish($destination, DIRECTORY_SEPARATOR) . "$classname.php";
        Console::debug("Destination: %s", $filename);

        try {
            $stub = $this->loadStub($stub_path);

            $stub = Str::replaceAll('$STUB_NAMESPACE', $namespace, $stub);
            $stub = Str::replaceAll('$STUB_CLASS_NAME', $classname, $stub);
            $stub = Str::replaceAll('$STUB_COMMAND_NAME', $name, $stub);

            $signature = $this->generateSignature($name, $arg_names);
            $stub = Str::replaceAll('$STUB_COMMAND_SIGNATURE', $signature, $stub);

            if (Str::isAbsolutePath($filename)) {
                if (realpath($filename) !== false) {
                    return CommandResult::fail(new FileNotCreatedException("File at $filename already exists"));
                }

                $success = false !== file_put_contents($filename, $stub);
            } else {
                $success = $this->files->get("app-root")->putContents($filename, $stub, overwrite: false);
            }

            if ($success) {
                Console::info("New command %s successfully generated at %s", Console::green($name), Console::link($filename));

                return CommandResult::success($filename);
            }

            return CommandResult::fail(new FileNotFoundException("Failed to put contents for new command at $filename."));
        } catch (FileNotFoundException | FileNotCreatedException | FileExistsException | NullReferenceException $e) {
            return CommandResult::fail($e);
        }
    }

    /**
     * @throws FileNotFoundException
     * @throws NullReferenceException
     */
    private function loadStub(?string $path): string
    {
        if ($path === null || strlen($path) == 0) {
            throw new NullReferenceException(var_name: 'path');
        }

        $p = realpath($path);
        if ($p === false || !file_exists($p)) {
            throw new FileNotFoundException(path: $path);
        }

        return file_get_contents($path);
    }

    #[Pure] private function generateSignature(string $command, array $arg_names): string
    {
        $sig = "new CommandSignature(\n            \"$command\",\n            [";

        foreach ($arg_names as $arg) {
            $sig .= "\n                new CommandArgumentTemplate(\"$arg\"),";
        }

        $sig .= "\n            ]\n        )";

        return $sig;
    }
}
