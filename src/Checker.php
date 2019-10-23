<?php

declare(strict_types = 1);

namespace TypesChecker;

use TypesChecker\Report\Report;

final class Checker
{
    /** @var array */
    private $excluded = [];

    /** @var bool */
    private $checkReturnTypes = true;

    /** @var ClassCollector */
    private $classCollector;

    /** @var Report */
    private $report;

    public function __construct(array $paths)
    {
        $this->classCollector = new ClassCollector($paths);
        $this->report = new Report();
    }

    public function exclude(string $name): void
    {
        $name = \str_replace('\\\\', '\\', $name);
        if (!\class_exists($name) && !\interface_exists($name) && !\trait_exists($name)) {
            throw new \InvalidArgumentException(\sprintf('Class, interface or trait "%s" does not exist.', $name));
        }
        $this->excluded[] = \ltrim($name, '\\');
    }

    public function skipReturnTypes(): void
    {
        $this->checkReturnTypes = false;
    }

    public function check(): Report
    {
        foreach ($this->classCollector->getClasses() as $class) {
            if ($this->isClassToCheck($class)) {
                $this->checkClass($class);
            }
        }

        return $this->report;
    }

    private function isClassToCheck(string $class): bool
    {
        return \count(\array_filter($this->excluded, static function (string $excluded) use ($class): bool {
            return $excluded === $class || \is_subclass_of($class, $excluded);
        })) === 0;
    }

    private function checkClass(string $class): void
    {
        $class = new \ReflectionClass($class);
        $this->report->addClass($class);

        foreach ($class->getMethods() as $method) {
            if ($this->isMethodToCheck($class, $method)) {
                $this->checkMethod($method);
            }
        }
    }

    private function isMethodToCheck(\ReflectionClass $class, \ReflectionMethod $method): bool
    {
        return $method->getFileName() === $class->getFileName()
            && $method->getStartLine() > $class->getStartLine()
            && $method->getEndLine() < $class->getEndLine();
    }

    private function checkMethod(\ReflectionMethod $method): void
    {
        if ($this->isMethodToCheckForReturnType($method) && $method->getReturnType() === null) {
            $this->report->addIssue($method, 'missing return type');
        }

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getType() === null) {
                $this->report->addIssue($method, \sprintf('parameter $%s is missing type', $parameter->getName()));
            }
        }
    }

    private function isMethodToCheckForReturnType(\ReflectionMethod $method): bool
    {
        return $this->checkReturnTypes && !\in_array($method->getName(), ['__construct', '__destruct', '__clone'], true);
    }
}
