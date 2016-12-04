<?php

namespace KubaWerlos\TypesChecker;

use Symfony\Component\Finder\Finder;

class Checker
{
    /** @var string */
    private $path;

    /** @var array */
    private $excludedInstances = [];

    /** @var bool */
    private $checkReturnTypes = true;

    /** @var Report */
    private $report;

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('Path "%s" does not exist.', $path));
        }
        $this->path = $path;
    }

    public function excludeInstance(string $name)
    {
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
        $this->report = new Report();

        $files = is_dir($this->path)
            ? (new Finder())
                ->files()
                ->filter(function (\SplFileInfo $file) {
                    return 'php' === $file->getExtension();
                })
                ->in([$this->path])
            : [new \SplFileInfo($this->path)];

        $classes = [];

        foreach ($files as $file) {
            $classes = array_merge($classes, $this->getClassesForFile($file->getRealPath()));
        }

        foreach ($classes as $class) {
            if (count(array_filter($this->excludedInstances, function ($name) use ($class) {
                return $name === $class || is_subclass_of($class, $name);
            })) === 0) {
                $this->testClass($class);
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

    private function testClass(string $class)
    {
        $reflection = new \ReflectionClass($class);

        foreach ($reflection->getMethods() as $method) {
            if ($method->class === $class) {
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
}
