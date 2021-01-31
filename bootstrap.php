<?php

declare(strict_types=1);

$autoload = "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
for ($i = 0; $i < 10 && realpath($autoload) === false; $i++) {
    $autoload = ".." . DIRECTORY_SEPARATOR . $autoload;
}

if (realpath($autoload) === false) {
    echo PHP_EOL . 'Could not find autoload.php' . PHP_EOL;

    exit(1);
}

/** @noinspection PhpIncludeInspection */
require $autoload;

/******************************************************************************/

use Philly\App;
use Philly\Contracts\Filesystem\FilesService;
use ricardoboss\Console;

// open console for logging
Console::open();
Console::debug("Bootstrapping application...");

// instantiate App container
$app = App::inst();

// store current dir as app root
$app[FilesService::class]->add('app-root', getcwd());

Console::debug("Bootstrapped application");
