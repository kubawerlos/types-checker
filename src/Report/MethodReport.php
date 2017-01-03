<?php

namespace KubaWerlos\TypesChecker\Report;

class MethodReport
{
    /** @var \ReflectionMethod */
    private $method;

    /** @var string[] */
    private $issues = [];

    public function __construct(\ReflectionMethod $method)
    {
        $this->method = $method;
    }

    public function addIssue(string $issue): self
    {
        $this->issues[] = $issue;

        return $this;
    }

    public function getName(): string
    {
        return $this->method->getName();
    }

    /**
     * @return string[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }
}
