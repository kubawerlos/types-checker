<?php

namespace KubaWerlos\TypesChecker\Report;

class ClassReport
{
    /** @var \ReflectionClass */
    private $class;

    /** @var MethodReport[] */
    private $methods = [];

    public function __construct(\ReflectionClass $class)
    {
        $this->class = $class;
    }

    public function addIssue(\ReflectionMethod $method, string $issue)
    {
        if (!isset($this->methods[$method->getName()])) {
            $this->methods[$method->getName()] = new MethodReport($method);
        }

        $this->methods[$method->getName()]->addIssue($issue);
    }

    public function getName(): string
    {
        switch (true) {
            case $this->class->isInterface():
                $type = 'Interface';
                break;
            case $this->class->isTrait():
                $type = 'Trait';
                break;
            default:
                $type = 'Class';
                break;
        }

        return sprintf('%s %s', $type, $this->class->getName());
    }

    /**
     * @return MethodReport[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getNumberOfIssues(): int
    {
        return array_sum(array_map(function (MethodReport $method): int {
            return count($method->getIssues());
        }, $this->methods));
    }

    public function hasIssues(): bool
    {
        return !empty($this->methods);
    }

    public function isClass(): bool
    {
        return !$this->isInterface() && !$this->isTrait();
    }

    public function isInterface(): bool
    {
        return $this->class->isInterface();
    }

    public function isTrait(): bool
    {
        return $this->class->isTrait();
    }
}
