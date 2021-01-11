<?php

/*
 * This file is part of Types checker.
 *
 * (c) 2016 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);
$config = PhpCsFixerConfig\Factory::createForLibrary('Types checker', 'Kuba Werłos', 2016)
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

$rules = $config->getRules();

unset($rules['use_arrow_functions']); // TODO: remove when dropping support to PHP <7.4

return $config->setRules($rules);
