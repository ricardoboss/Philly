---
layout: default
title: Command Line
nav_order: 40
has_children: false
has_toc: false
---

# Command Line Interface
{: .no_toc}

## Table of contents
{: .no_toc .text-delta}

1. TOC
{:toc}

---

## Introduction

The command line can be accessed by simply typing `bin/philly` in the main directory.

At the moment, only basic commands like `bin/philly version` are supported.

## Adding Commands

To create your own commands, you need to extend the `Philly\CLI\Commands\Command` class:

```php
use Philly\App;
use Philly\CLI\Commands\Command;
use Philly\CLI\Commands\CommandResult;
use Philly\CLI\Commands\CommandSignature;
use Philly\Contracts\CLI\Commands\CommandArgumentCollection as CommandArgumentCollectionContract;
use Philly\Contracts\CLI\Commands\CommandResult as CommandResultContract;

class VersionCommand extends Command
{
    public function __construct()
    {
        parent::__construct(new CommandSignature("version"));
    }

    public function handle(CommandArgumentCollectionContract $args): CommandResultContract
    {
        return CommandResult::success(App::VERSION);
    }
}
```

As you can see, you have to create a `CommandSignature` to pass metadata to the parent constructor.
The signature contains the name of your command as well as aliases and arguments.

The `handle` method will get invoked if the user executes `bin/philly version`.
Here, you can implement the logic of your command using the `CommandArgumentCollection` and return a `CommandResult`.

The arguments get parsed and matched to the arguments registered by the command signature.
Only arguments registered in the signature will be included in the argument collection.

This result not only tells if the command was successfully executed, it can also hold additional data like the result of the command.

## Registering Commands

You can simply register your own commands using `App::instance()->getCommands()->add(new VersionCommand())`.
