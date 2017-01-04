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

Console:

```bash
    vendor/bin/types-checker src tests
```

PHP:

```php
    $checker = new KubaWerlos\TypesChecker\Checker(['src', 'tests']);

    $report = $checker->check();

    if ($report->hasIssues()) {
        print_r($report->getClasses());
    }
```


## Configuration

 console             | PHP                                |                                               |
 ------------------- | ---------------------------------- | --------------------------------------------- |
 --exclude Foo       | $checker->excludeClass(Foo::class) | Exclude class, interface or trait from report |
 --skip-return-types | $checker->skipReturnTypes();       | Do not report missing return types            |


## Example

```php
    <?php
    
    interface Foo
    {
        public function baz();
    }
    
    class Bar
    {
        public function baz($x): array
        {
        }
    
        public function qux(bool $b, $x)
        {
        }
    }

```

```bash
    Types checker - 2 items checked:
     - 1 class
     - 1 interface
    
    Issues found:
     - Interface Foo:
       - baz:
         - missing return type
     - Class Bar:
       - baz:
         - parameter $x is missing type
       - qux:
         - missing return type
         - parameter $x is missing type
    
      4 issues
```
