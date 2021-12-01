<?php declare(strict_types=1);

/*
 * This file is part of Types checker.
 *
 * (c) 2016 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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

unset($rules['phpdoc_tag_type']); // TODO: remove after bug is fixed: https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/5395
unset($rules['use_arrow_functions']); // TODO: remove when dropping support to PHP <7.4
unset($rules['modernize_strpos']); // TODO: remove when dropping support to PHP <8.0
unset($rules[PhpCsFixerCustomFixers\Fixer\PromotedConstructorPropertyFixer::name()]); // TODO: remove when dropping support to PHP <8.0
$rules['trailing_comma_in_multiline'] = true; // TODO: remove when dropping support to PHP <8.0

return $config->setRules($rules);
