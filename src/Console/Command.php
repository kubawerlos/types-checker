<?php

namespace KubaWerlos\TypesChecker\Console;

use KubaWerlos\TypesChecker\Checker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Symfony\Component\Console\Command\Command
{
    const NAME = 'types-checker';

    protected function configure()
    {
        $this->setName(self::NAME)
            ->addArgument(
                'path',
                InputArgument::IS_ARRAY
            )
            ->addOption(
                'exclude-instance',
                'e',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Exclude class or interface instances from report'
            )
            ->addOption(
                'skip-return-types',
                's',
                InputOption::VALUE_NONE,
                'Do not report missing return types'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $checker = new Checker($input->getArgument('path'));

        foreach ($input->getOption('exclude-instance') as $name) {
            $checker->exclude($name);
        }

        if ($input->getOption('skip-return-types')) {
            $checker->skipReturnTypes();
        }

        $report = $checker->check();

        $output->writeln('Type declarations issues');
        $output->writeln('');

        if ($report->isProper()) {
            $output->writeln(' * nothing found');

            return 0;
        } else {
            foreach ($report->getErrors() as $class => $errors) {
                $output->writeln(sprintf(' * class %s:', $class));
                foreach ($errors as $error) {
                    $output->writeln(sprintf('   - %s', $error));
                }
            }

            return 1;
        }
    }
}
