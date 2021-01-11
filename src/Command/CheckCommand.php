<?php

/*
 * This file is part of Types checker.
 *
 * (c) 2016 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace TypesChecker\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TypesChecker\Checker;
use TypesChecker\Report\Report;

final class CheckCommand extends BaseCommand
{
    protected static $defaultName = 'types-checker';

    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::IS_ARRAY)
            ->addOption('autoloader', 'a', InputOption::VALUE_REQUIRED, 'Add custom autoloader file')
            ->addOption('exclude', 'e', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Exclude class, interface or trait from report')
            ->addOption('skip-return-types', 's', InputOption::VALUE_NONE, 'Do not report missing return types');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $paths */
        $paths = $input->getArgument('path');

        /** @var null|string $autoloader */
        $autoloader = $input->getOption('autoloader');
        if ($autoloader !== null) {
            if (!\file_exists($autoloader)) {
                throw new \InvalidArgumentException(\sprintf('File "%s" does not exist.', $autoloader));
            }
            require_once $autoloader;
        }

        $checker = new Checker($paths);

        /** @var string[] $excludes */
        $excludes = $input->getOption('exclude');
        foreach ($excludes as $name) {
            $checker->exclude($name);
        }

        /** @var bool $skipReturnTypes */
        $skipReturnTypes = $input->getOption('skip-return-types');
        if ($skipReturnTypes) {
            $checker->skipReturnTypes();
        }

        $report = $checker->check();

        $output->writeln('');

        $whatWasChecked = $this->getWhatWasChecked($report);
        if (\strpos($whatWasChecked, 'item') !== false) {
            $output->writeln(\sprintf('Types checker - %s checked:', $whatWasChecked));
            if ($report->getNumberOfClasses() > 0) {
                $output->writeln(\sprintf(' - %s', $this->pluralize($report->getNumberOfClasses(), 'class')));
            }
            if ($report->getNumberOfInterfaces() > 0) {
                $output->writeln(\sprintf(' - %s', $this->pluralize($report->getNumberOfInterfaces(), 'interface')));
            }
            if ($report->getNumberOfTraits() > 0) {
                $output->writeln(\sprintf(' - %s', $this->pluralize($report->getNumberOfTraits(), 'trait')));
            }
        } else {
            $output->writeln(\sprintf('Types checker - %s checked.', $whatWasChecked));
        }

        $output->writeln('');

        if (!$report->hasIssues()) {
            $output->writeln('  No issues found.');
            $output->writeln('');

            return 0;
        }

        $output->writeln('Issues found:');

        foreach ($report->getClasses() as $class) {
            $output->writeln(\sprintf(' - %s:', $class->getName()));
            foreach ($class->getMethods() as $method) {
                $output->writeln(\sprintf('   - %s:', $method->getName()));
                foreach ($method->getIssues() as $issue) {
                    $output->writeln(\sprintf('     - %s', $issue));
                }
            }
        }
        $output->writeln('');

        $output->writeln(\sprintf('  %s', $this->pluralize($report->getNumberOfIssues(), 'issue')));
        $output->writeln('');

        return 1;
    }

    private function getWhatWasChecked(Report $report): string
    {
        if ($report->getNumberOfItems() > 0) {
            if ($report->getNumberOfItems() === $report->getNumberOfClasses()) {
                return $this->pluralize($report->getNumberOfClasses(), 'class');
            }
            if ($report->getNumberOfItems() === $report->getNumberOfInterfaces()) {
                return $this->pluralize($report->getNumberOfInterfaces(), 'interface');
            }
            if ($report->getNumberOfItems() === $report->getNumberOfTraits()) {
                return $this->pluralize($report->getNumberOfTraits(), 'trait');
            }
        }

        return $this->pluralize($report->getNumberOfItems(), 'item');
    }

    private function pluralize(int $count, string $name): string
    {
        if ($count !== 1) {
            $name .= \substr($name, -1) === 's' ? 'es' : 's';
        }

        return \sprintf('%d %s', $count, $name);
    }
}
