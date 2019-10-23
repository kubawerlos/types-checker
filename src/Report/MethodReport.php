<?php

declare(strict_types = 1);

namespace TypesChecker\Report;

final class MethodReport
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
