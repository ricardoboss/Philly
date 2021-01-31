<?php

declare(strict_types=1);

namespace Philly\Foundation\CLI\Commands;

use Philly\App;
use Philly\CLI\Commands\Command;
use Philly\CLI\Commands\CommandArgumentTemplate;
use Philly\CLI\Commands\CommandResult;
use Philly\CLI\Commands\CommandSignature;
use Philly\Contracts\CLI\Commands\CommandArgumentCollection as CommandArgumentCollectionContract;
use Philly\Contracts\CLI\Commands\CommandResult as CommandResultContract;
use Philly\Contracts\Filesystem\FilesService;
use Philly\Filesystem\FileNotFoundException;
use ricardoboss\Console;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class ServeCommand.
 */
class ServeCommand extends Command
{
    private FilesService $filesService;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct(new CommandSignature(
            "serve",
            [
                new CommandArgumentTemplate("port", optional: true, default: 8000),
                new CommandArgumentTemplate("host", optional: true, default: "127.0.0.1")
            ]
        ));

        $this->filesService = App::inst()->getServices()->get(FilesService::class);
    }

    /**
     * @inheritDoc
     * @throws FileNotFoundException
     */
    public function handle(CommandArgumentCollectionContract $args): CommandResultContract
    {
        /** @var int $port */
        $port = $args->getValue('port');

        /** @var string $host */
        $host = $args->getValue('host');

        $root = $this->filesService->get('app-root')->real('public', false);

        $command = $this->buildCommand($root, $host, $port);

        $process = new Process($command, timeout: null);
        $process->start();

        $exitCode = $process->wait(function ($type, $buffer) {
            $messages = array_filter(
                mb_split("\r?\n", $buffer),
                fn ($v) => strlen($v) > 0,
                ARRAY_FILTER_USE_BOTH
            );

            if ($type === Process::ERR) {
                foreach ($messages as $message) {
                    Console::error($message);
                }
            } else {
                foreach ($messages as $message) {
                    Console::info($message);
                }
            }
        });

        return $exitCode === 0 ? CommandResult::success() : CommandResult::fail();
    }

    private function buildCommand(?string $root, string $host, int $port): array
    {
        $result = [
            (new PhpExecutableFinder())->find(false),
            "-S",
            "$host:$port"
        ];

        if ($root !== null) {
            array_push($result, "-t", $root);
        }

        return $result;
    }
}
