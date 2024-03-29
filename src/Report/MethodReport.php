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

final class MethodReport
{
    /** @var \ReflectionMethod */
    private $method;

    /** @var array<string> */
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
     * @return array<string>
     */
    public function getIssues(): array
    {
        return $this->issues;
    }
}
