<?php

namespace KubaWerlos\TypesChecker\Report;

class Report
{
    /** @var ClassReport[] */
    private $classes = [];

    public function addClass(\ReflectionClass $class)
    {
        $this->getClass($class);
    }

    public function addIssue(\ReflectionMethod $method, string $issue)
    {
        $this->getClass($method->getDeclaringClass())->addIssue($method, $issue);
    }

    private function getClass(\ReflectionClass $class): ClassReport
    {
        if (!isset($this->classes[$class->getName()])) {
            $this->classes[$class->getName()] = new ClassReport($class);
        }

        return $this->classes[$class->getName()];
    }

    /**
     * @return ClassReport[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getNumberOfItems(): int
    {
        return count($this->classes);
    }

    public function getNumberOfClasses(): int
    {
        return count(array_filter($this->classes, function (ClassReport $class): bool {
            return $class->isClass();
        }));
    }

    public function getNumberOfInterfaces(): int
    {
        return count(array_filter($this->classes, function (ClassReport $class): bool {
            return $class->isInterface();
        }));
    }

    public function getNumberOfTraits(): int
    {
        return count(array_filter($this->classes, function (ClassReport $class): bool {
            return $class->isTrait();
        }));
    }

    public function getNumberOfIssues(): int
    {
        return array_sum(array_map(function (ClassReport $class): int {
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
}
