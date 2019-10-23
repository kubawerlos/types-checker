<?php

declare(strict_types = 1);

namespace KubaWerlos\TypesChecker\Console;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

final class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct(Command::NAME);
    }

    public function getDefinition(): InputDefinition
    {
        $definition = parent::getDefinition();
        $definition->setArguments();

        return $definition;
    }

    protected function getCommandName(InputInterface $input): string
    {
        return Command::NAME;
    }

    protected function getDefaultCommands(): array
    {
        return \array_merge(parent::getDefaultCommands(), [new Command()]);
    }
}
