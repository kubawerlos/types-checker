<?php

declare(strict_types=1);

/*
 * This file is part of Types checker.
 *
 * (c) Kuba Werłos <werlos@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace TypesChecker\Report;

final class ClassReport
{
    /** @var \ReflectionClass */
    private $class;

    /** @var MethodReport[] */
    private $methods = [];

    public function __construct(\ReflectionClass $class)
    {
        $this->class = $class;
    }

    public function addIssue(\ReflectionMethod $method, string $issue): void
    {
        if (!isset($this->methods[$method->getName()])) {
            $this->methods[$method->getName()] = new MethodReport($method);
        }

        $this->methods[$method->getName()]->addIssue($issue);
    }

    public function getName(): string
    {
        if ($this->class->isInterface()) {
            $type = 'Interface';
        } elseif ($this->class->isTrait()) {
            $type = 'Trait';
        } else {
            $type = 'Class';
        }

        return \sprintf('%s %s', $type, $this->class->getName());
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
        return (int) \array_sum(\array_map(static function (MethodReport $method): int {
            return \count($method->getIssues());
        }, $this->methods));
    }

    public function hasIssues(): bool
    {
        return $this->methods !== [];
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
