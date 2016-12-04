# Kuba Werlos / Types checker

[![Latest Stable Version](https://img.shields.io/packagist/v/kubawerlos/types-checker.svg)](https://packagist.org/packages/kubawerlos/types-checker)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207-8892BF.svg)](https://php.net)
[![License](https://img.shields.io/github/license/kubawerlos/types-checker.svg)](https://packagist.org/packages/kubawerlos/types-checker)
[![Build Status](https://travis-ci.org/kubawerlos/types-checker.svg?branch=master)](https://travis-ci.org/kubawerlos/types-checker)

A tool to find missing type declarations in PHP 7 code.

## Installation
```bash
    composer require kubawerlos/types-checker
```

## Usage
```php
    <?php

    use KubaWerlos\TypesChecker\Checker;

    $checker = new Checker(__DIR__.'/../src');

    $report = $checker->check();

    $report->isProper();
```

or from command line:
```bash
    ./types-checker src
```

## Configuration
 $checker                     | ./types-checker     |
 ---------------------------- | ------------------- | ----------------------------------
 $checker->skipReturnTypes(); | --skip-return-types | Do not report missing return types
