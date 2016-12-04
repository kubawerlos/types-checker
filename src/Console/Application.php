<?php

namespace KubaWerlos\TypesChecker\Console;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct(Command::NAME);
        $this->setDefaultCommand(Command::NAME, true);
    }

    protected function getDefaultCommands(): array
    {
        return array_merge(parent::getDefaultCommands(), [new Command()]);
    }
}
