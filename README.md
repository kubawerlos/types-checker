# Kuba Werłos / Types checker

[![Latest Stable Version](https://img.shields.io/packagist/v/kubawerlos/types-checker.svg)](https://packagist.org/packages/kubawerlos/types-checker)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%207-8892BF.svg)](https://php.net)
[![License](https://img.shields.io/github/license/kubawerlos/types-checker.svg)](https://packagist.org/packages/kubawerlos/types-checker)
[![Build Status](https://img.shields.io/travis/kubawerlos/types-checker/master.svg)](https://travis-ci.org/kubawerlos/types-checker)

A tool to find missing type declarations in PHP 7 code.

## Installation
```bash
    composer require --dev kubawerlos/types-checker
```

## Usage
PHP:
```php
    $checker = new KubaWerlos\TypesChecker\Checker(['src', 'tests']);

    $report = $checker->check();

    if (!$report->isProper()) {
        print_r($report->getErrors());
    }
```

Console:
```bash
    vendor/bin/types-checker src tests
```

## Configuration
 PHP                                | console             |                                               |
 ---------------------------------- | ------------------- | --------------------------------------------- |
 $checker->excludeClass(Foo::class) | --exclude Foo       | Exclude class, interface or trait from report |
 $checker->skipReturnTypes();       | --skip-return-types | Do not report missing return types            |
