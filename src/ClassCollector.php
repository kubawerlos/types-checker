<?php

declare(strict_types = 1);

namespace TypesChecker;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class ClassCollector
{
    /** @var string[] */
    private $classes = [];

    /**
     * @param string[] $paths
     */
    public function __construct(array $paths)
    {
        $files = [];
        foreach ($paths as $path) {
            $realPath = \realpath($path);
            if ($realPath === false) {
                throw new \InvalidArgumentException(\sprintf('Path "%s" does not exist.', $path));
            }
            if (\is_dir($realPath)) {
                $files = \array_merge(
                    $files,
                    \iterator_to_array(
                        (new Finder())
                            ->files()
                            ->filter(static function (\SplFileInfo $file): bool {
                                return $file->getExtension() === 'php';
                            })
                            ->in($realPath)
                    )
                );
            } else {
                $files[] = $realPath;
            }
        }

        foreach ($files as $file) {
            if ($file instanceof SplFileInfo) {
                /** @var string $file */
                $file = $file->getRealPath();
            }
            foreach ($this->getClassesForFile($file) as $class) {
                $this->classes[\ltrim($class, '\\')] = $file;
            }
        }

        \spl_autoload_register(function ($class): void {
            if (isset($this->classes[$class])) {
                require_once $this->classes[$class];
            }
        });
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return \array_keys($this->classes);
    }

    /**
     * @return string[]
     */
    private function getClassesForFile(string $path): array
    {
        /** @var string $content */
        $content = \file_get_contents($path);

        $tokens = \token_get_all($content);

        $classes = [];

        $namespace = '';

        $count = \count($tokens);

        $i = 1;

        while ($i < $count) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                $i += 2;
                while (isset($tokens[$i]) && \is_array($tokens[$i])) {
                    if (\in_array($tokens[$i][0], [T_NS_SEPARATOR, T_STRING], true)) {
                        $namespace .= $tokens[$i][1];
                    }
                    $i++;
                }
            }
            if (\in_array($tokens[$i][0], [T_CLASS, T_INTERFACE, T_TRAIT], true)
                && $tokens[$i + 1][0] === T_WHITESPACE
                && $tokens[$i + 2][0] === T_STRING) {
                $classes[] = \sprintf('%s\\%s', $namespace, $tokens[$i + 2][1]);
            }
            $i++;
        }

        return $classes;
    }
}
