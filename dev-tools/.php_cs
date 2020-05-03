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

return PhpCsFixerConfig\Factory::createForLibrary('Types checker')
    ->setUsingCache(false)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->files()
            ->in(__DIR__ . '/../src')
            ->in(__DIR__ . '/../tests')
            ->notPath('_stubs')
            ->append([
                __FILE__,
                __DIR__ . '/../types-checker',
            ])
    );
