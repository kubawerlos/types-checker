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
                'exclude',
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

        foreach ($input->getOption('exclude') as $name) {
            $checker->exclude($name);
        }

        if ($input->getOption('skip-return-types')) {
            $checker->skipReturnTypes();
        }

        $report = $checker->check();

        $output->writeln(sprintf('Types checker - %d items checked, issues:', $report->getNumberOfClasses()));
        $output->writeln('');

        if (!$report->hasIssues()) {
            $output->writeln('Nothing found!');

            return 0;
        }

        $count = 0;
        foreach ($report->getClasses() as $class) {
            if ($class->hasIssues()) {
                $output->writeln(sprintf(' - %s:', $class->getName()));
                foreach ($class->getMethods() as $method) {
                    $output->writeln(sprintf('   - %s:', $method->getName()));
                    foreach ($method->getIssues() as $issue) {
                        $output->writeln(sprintf('     - %s', $issue));
                        ++$count;
                    }
                }
            }
        }

        return 1;
    }
}
