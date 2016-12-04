<?php

namespace KubaWerlos\TypesChecker;

use Symfony\Component\Finder\Finder;

class Checker
{
    /** @var array */
    private $files = [];

    /** @var array */
    private $excludedInstances = [];

    /** @var bool */
    private $checkReturnTypes = true;

    /** @var Report */
    private $report;

    public function __construct(array $paths)
    {
        foreach ($paths as $path) {
            $path = realpath($path);
            if (!file_exists($path)) {
                throw new \InvalidArgumentException(sprintf('Path "%s" does not exist.', $path));
            }
            if (is_dir($path)) {
                // check
                $this->files = array_merge(
                    $this->files,
                    iterator_to_array((new Finder())
                        ->files()
                        ->filter(function (\SplFileInfo $file) {
                            return 'php' === $file->getExtension();
                        })
                        ->in($path))
                );
            } else {
                $this->files[] = $path;
            }
        }

        $this->report = new Report();
    }

    public function exclude(string $name)
    {
        $name = str_replace('\\\\', '\\', $name);
        if (!class_exists($name) && !interface_exists($name)) {
            throw new \InvalidArgumentException(sprintf('Class or interface "%s" does not exist.', $name));
        }
        $this->excludedInstances[] = $name;
    }

    public function skipReturnTypes()
    {
        $this->checkReturnTypes = false;
    }

    public function check(): Report
    {
        foreach ($this->files as $file) {
            foreach ($this->getClassesForFile($file) as $class) {
                if ($this->isClassToCheck($class)) {
                    $this->checkClass($class);
                    $this->report->incrementItemsCount();
                }
            }
        }

        return $this->report;
    }

    private function getClassesForFile(string $path): array
    {
        $tokens = token_get_all(file_get_contents($path));

        $classes = [];

        $namespace = '';

        $count = count($tokens);

        for ($i = 2; $i < $count; ++$i) {
            if (T_NAMESPACE === $tokens[$i][0]) {
                $i += 2;
                while (isset($tokens[$i]) && is_array($tokens[$i])) {
                    $namespace .= $tokens[$i++][1];
                }
            }
            if ($tokens[$i - 2][0] === T_CLASS && $tokens[$i - 1][0] === T_WHITESPACE && $tokens[$i][0] === T_STRING) {
                $className = $tokens[$i][1];
                $classes[] = $namespace.'\\'.$className;
            }
        }

        return $classes;
    }

    private function isClassToCheck(string $name): bool
    {
        return count(array_filter($this->excludedInstances, function ($excluded) use ($name) {
            return $excluded === $name || is_subclass_of($name, $excluded);
        })) === 0;
    }

    private function checkClass(string $class)
    {
        $reflection = new \ReflectionClass($class);

        foreach ($reflection->getMethods() as $method) {
            if ($this->isMethodToCheck($method->getName()) && $method->class === $class) {
                foreach ($method->getParameters() as $parameter) {
                    if ($parameter->getType() === null) {
                        $this->report->addErrors(
                            $class,
                            sprintf('%s - parameter $%s is missing type', $method->getName(), $parameter->getName())
                        );
                    }
                }
                if ($this->checkReturnTypes && $method->getReturnType() === null) {
                    $this->report->addErrors($class, "{$method->getName()} is missing return type");
                }
            }
        }
    }

    private function isMethodToCheck(string $class)
    {
        return !in_array($class, ['__construct', '__destruct', '__clone'], true);
    }
}
