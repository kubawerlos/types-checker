<?php declare(strict_types=1);

/*
 * This file is part of Types checker.
 *
 * (c) 2016 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace TypesChecker;

use Symfony\Component\Finder\Finder;

final class ClassCollector
{
    /** @var array<string, string> */
    private $classes = [];

    /**
     * @param array<string> $paths
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
                $finder = Finder::create()
                    ->files()
                    ->name('*.php')
                    ->in($realPath);

                foreach ($finder as $file) {
                    $path = $file->getRealPath();
                    \assert(\is_string($path));
                    $files[] = $path;
                }
            } else {
                $files[] = $realPath;
            }
        }

        foreach ($files as $file) {
            foreach ($this->getClassesForFile($file) as $class) {
                $this->classes[\ltrim($class, '\\')] = $file;
            }
        }

        \spl_autoload_register(function (string $class): void {
            if (\array_key_exists($class, $this->classes)) {
                require_once $this->classes[$class];
            }
        });
    }

    /**
     * @return array<string>
     */
    public function getClasses(): array
    {
        return \array_keys($this->classes);
    }

    /**
     * @return array<string>
     */
    private function getClassesForFile(string $path): array
    {
        $content = \file_get_contents($path);
        \assert(\is_string($content));

        $tokens = $this->getTokens($content);

        $classes = [];

        $namespace = '';

        $count = \count($tokens);

        $i = 1;

        while ($i < $count) {
            if ($tokens[$i][0] === \T_NAMESPACE) {
                $i += 2;
                while (\array_key_exists($i, $tokens) && \is_array($tokens[$i])) {
                    if (\in_array($tokens[$i][0], [\T_NS_SEPARATOR, \T_STRING], true)) {
                        $namespace .= $tokens[$i][1];
                    }
                    $i++;
                }
            }
            if (
                \in_array($tokens[$i][0], [\T_CLASS, \T_INTERFACE, \T_TRAIT], true)
                && $tokens[$i + 1][0] === \T_WHITESPACE
                && $tokens[$i + 2][0] === \T_STRING
            ) {
                $classes[] = \sprintf('%s\\%s', $namespace, $tokens[$i + 2][1]);
            }
            $i++;
        }

        return $classes;
    }

    /**
     * @return array<int, array{0: int, 1: string, 2?: int}|string>
     */
    private function getTokens(string $content): array
    {
        $tokens = [];

        foreach (\token_get_all($content) as $token) {
            if (\defined('T_NAME_QUALIFIED') && \is_array($token) && \in_array($token[0], [\T_NAME_QUALIFIED, \T_NAME_FULLY_QUALIFIED], true)) {
                $parts = \explode('\\', $token[1]);

                if ($parts[0] === '') {
                    $tokens[] = [\T_NS_SEPARATOR, '\\'];
                    \array_shift($parts);
                }

                foreach ($parts as $part) {
                    $tokens[] = [\T_STRING, $part];
                    $tokens[] = [\T_NS_SEPARATOR, '\\'];
                }

                \array_pop($tokens);

                continue;
            }

            $tokens[] = $token;
        }

        return $tokens;
    }
}
