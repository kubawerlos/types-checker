<?php

namespace KubaWerlos\TypesChecker\Console;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct(Command::NAME);
    }

    protected function getCommandName(InputInterface $input): string
    {
        return Command::NAME;
    }

    protected function getDefaultCommands(): array
    {
        return array_merge(parent::getDefaultCommands(), [new Command()]);
    }

    public function getDefinition(): InputDefinition
    {
        $definition = parent::getDefinition();
        $definition->setArguments();

        return $definition;
    }
}
