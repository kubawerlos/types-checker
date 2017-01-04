<?php

namespace KubaWerlos\TypesChecker\Console;

use KubaWerlos\TypesChecker\Checker;
use KubaWerlos\TypesChecker\Report\Report;
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

        $output->writeln('');

        $whatWasChecked = $this->getWhatWasChecked($report);
        if (mb_strpos($whatWasChecked, 'item') !== false) {
            $output->writeln(sprintf('Types checker - %s checked:', $whatWasChecked));
            if ($report->getNumberOfClasses() > 0) {
                $output->writeln(sprintf(' - %s', $this->pluralize($report->getNumberOfClasses(), 'class')));
            }
            if ($report->getNumberOfInterfaces() > 0) {
                $output->writeln(sprintf(' - %s', $this->pluralize($report->getNumberOfInterfaces(), 'interface')));
            }
            if ($report->getNumberOfTraits() > 0) {
                $output->writeln(sprintf(' - %s', $this->pluralize($report->getNumberOfTraits(), 'trait')));
            }
        } else {
            $output->writeln(sprintf('Types checker - %s checked.', $whatWasChecked));
        }

        $output->writeln('');

        if (!$report->hasIssues()) {
            $output->writeln('  No issues found.');
            $output->writeln('');

            return 0;
        }

        $output->writeln('Issues found:');

        foreach ($report->getClasses() as $class) {
            if ($class->hasIssues()) {
                $output->writeln(sprintf(' - %s:', $class->getName()));
                foreach ($class->getMethods() as $method) {
                    $output->writeln(sprintf('   - %s:', $method->getName()));
                    foreach ($method->getIssues() as $issue) {
                        $output->writeln(sprintf('     - %s', $issue));
                    }
                }
            }
        }
        $output->writeln('');

        $output->writeln(sprintf('  %s', $this->pluralize($report->getNumberOfIssues(), 'issue')));
        $output->writeln('');

        return 1;
    }

    private function getWhatWasChecked(Report $report): string
    {
        if ($report->getNumberOfItems() > 0) {
            if ($report->getNumberOfItems() === $report->getNumberOfClasses()) {
                return $this->pluralize($report->getNumberOfClasses(), 'class');
            } elseif ($report->getNumberOfItems() === $report->getNumberOfInterfaces()) {
                return $this->pluralize($report->getNumberOfInterfaces(), 'interface');
            } elseif ($report->getNumberOfItems() === $report->getNumberOfTraits()) {
                return $this->pluralize($report->getNumberOfTraits(), 'trait');
            }
        }

        return $this->pluralize($report->getNumberOfItems(), 'item');
    }

    private function pluralize(int $count, string $name): string
    {
        if ($count !== 1) {
            $name .= mb_substr($name, -1) === 's' ? 'es' : 's';
        }

        return sprintf('%d %s', $count, $name);
    }
}
