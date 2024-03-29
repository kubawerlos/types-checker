<?php declare(strict_types=1);

/*
 * This file is part of Types checker.
 *
 * (c) 2016 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace TypesChecker\Report;

final class Report
{
    /** @var array<ClassReport> */
    private $classes = [];

    public function addClass(\ReflectionClass $class): void
    {
        $this->getClass($class);
    }

    public function addIssue(\ReflectionMethod $method, string $issue): void
    {
        $this->getClass($method->getDeclaringClass())->addIssue($method, $issue);
    }

    /**
     * @return array<ClassReport>
     */
    public function getClasses(): array
    {
        return \array_filter($this->classes, static function (ClassReport $class): bool {
            return $class->hasIssues();
        });
    }

    public function getNumberOfItems(): int
    {
        return \count($this->classes);
    }

    public function getNumberOfClasses(): int
    {
        return \count(\array_filter($this->classes, static function (ClassReport $class): bool {
            return $class->isClass();
        }));
    }

    public function getNumberOfInterfaces(): int
    {
        return \count(\array_filter($this->classes, static function (ClassReport $class): bool {
            return $class->isInterface();
        }));
    }

    public function getNumberOfTraits(): int
    {
        return \count(\array_filter($this->classes, static function (ClassReport $class): bool {
            return $class->isTrait();
        }));
    }

    public function getNumberOfIssues(): int
    {
        return \array_sum(\array_map(static function (ClassReport $class): int {
            return $class->getNumberOfIssues();
        }, $this->classes));
    }

    public function hasIssues(): bool
    {
        foreach ($this->classes as $class) {
            if ($class->hasIssues()) {
                return true;
            }
        }

        return false;
    }

    private function getClass(\ReflectionClass $class): ClassReport
    {
        if (!\array_key_exists($class->getName(), $this->classes)) {
            $this->classes[$class->getName()] = new ClassReport($class);
        }

        return $this->classes[$class->getName()];
    }
}
