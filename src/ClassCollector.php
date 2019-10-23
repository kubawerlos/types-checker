<?php

namespace KubaWerlos\TypesChecker;

use Symfony\Component\Finder\Finder;

class ClassCollector
{
    /** @var array */
    private $classes = [];

    public function __construct(array $paths)
    {
        $files = [];
        foreach ($paths as $path) {
            $realPath = realpath($path);
            if ($realPath === false) {
                throw new \InvalidArgumentException(sprintf('Path "%s" does not exist.', $path));
            }
            if (is_dir($realPath)) {
                $files = array_merge(
                    $files,
                    iterator_to_array((new Finder())
                        ->files()
                        ->filter(function (\SplFileInfo $file) {
                            return 'php' === $file->getExtension();
                        })
                        ->in($realPath))
                );
            } else {
                $files[] = $realPath;
            }
        }

        foreach ($files as $file) {
            foreach ($this->getClassesForFile($file) as $class) {
                $this->classes[ltrim($class, '\\')] = $file;
            }
        }

        spl_autoload_register(function ($class) {
            if (isset($this->classes[$class])) {
                require_once $this->classes[$class];
            }
        });
    }

    public function getClasses(): array
    {
        return array_keys($this->classes);
    }

    private function getClassesForFile(string $path): array
    {
        $tokens = token_get_all(file_get_contents($path));

        $classes = [];

        $namespace = '';

        $count = count($tokens);

        $i = 1;

        while ($i < $count) {
            if (T_NAMESPACE === $tokens[$i][0]) {
                $i += 2;
                while (isset($tokens[$i]) && is_array($tokens[$i])) {
                    if (in_array($tokens[$i][0], [T_NS_SEPARATOR, T_STRING])) {
                        $namespace .= $tokens[$i][1];
                    }
                    $i++;
                }
            }
            if (in_array($tokens[$i][0], [T_CLASS, T_INTERFACE, T_TRAIT], true)
                && $tokens[$i + 1][0] === T_WHITESPACE
                && $tokens[$i + 2][0] === T_STRING) {
                $classes[] = sprintf('%s\\%s', $namespace, $tokens[$i + 2][1]);
            }
            ++$i;
        }

        return $classes;
    }
}
