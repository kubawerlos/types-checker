<?php

namespace KubaWerlos\TypesChecker;

use KubaWerlos\TypesChecker\Report\Report;

class Checker
{
    /** @var array */
    private $excludedInstances = [];

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

    public function exclude(string $name)
    {
        $name = str_replace('\\\\', '\\', $name);
        if (!class_exists($name) && !interface_exists($name) && !trait_exists($name)) {
            throw new \InvalidArgumentException(sprintf('Class, interface or trait "%s" does not exist.', $name));
        }
        $this->excludedInstances[] = $name;
    }

    public function skipReturnTypes()
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

    private function isClassToCheck(string $name): bool
    {
        return count(array_filter($this->excludedInstances, function ($excluded) use ($name) {
            return $excluded === $name || is_subclass_of($name, $excluded);
        })) === 0;
    }

    private function checkClass(string $class)
    {
        $class = new \ReflectionClass($class);
        $this->report->addClass($class);

        foreach ($class->getMethods() as $method) {
            if ($this->isMethodToCheck($class, $method)) {
                if ($this->checkReturnTypes && $method->getReturnType() === null) {
                    $this->report->addIssue($method, 'missing return type');
                }
                foreach ($method->getParameters() as $parameter) {
                    if ($parameter->getType() === null) {
                        $this->report->addIssue(
                            $method,
                            sprintf('parameter $%s is missing type', $parameter->getName())
                        );
                    }
                }
            }
        }
    }

    private function isMethodToCheck(\ReflectionClass $class, \ReflectionMethod $method): bool
    {
        return $method->getFileName() === $class->getFileName()
            && $method->getStartLine() > $class->getStartLine()
            && $method->getEndLine() < $class->getEndLine()
            && !in_array($method->getName(), ['__construct', '__destruct', '__clone'], true);
    }
}
