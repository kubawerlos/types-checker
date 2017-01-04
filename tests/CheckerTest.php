<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Checker
 */
class CheckerTest extends TestCase
{
    public function properFilesProvider()
    {
        return [
            [__DIR__.'/_stubs/ChildClass.php'],
            [__DIR__.'/_stubs/ProperClass.php'],
            [__DIR__.'/_stubs/ProperInterface.php'],
            [__DIR__.'/_stubs/ProperTrait.php'],
            [__DIR__.'/_stubs/HavingImproperTraitProperClass.php'],
            [__DIR__.'/_stubs/HavingImproperTraitProperOverridingClass.php'],
        ];
    }

    /**
     * @dataProvider properFilesProvider
     */
    public function testProperFile(string $path)
    {
        $checker = new Checker([$path]);

        $report = $checker->check();

        $this->assertFalse($report->hasIssues());
        $this->assertSame(1, $report->getNumberOfItems());
    }

    public function improperFilesProvider()
    {
        return [
            [__DIR__.'/_stubs/HavingProperTraitImproperClass.php'],
            [__DIR__.'/_stubs/HavingProperTraitImproperOverridingClass.php'],
            [__DIR__.'/_stubs/MissingParameterTypeClass.php'],
            [__DIR__.'/_stubs/MissingParameterTypeInterface.php'],
            [__DIR__.'/_stubs/MissingParameterTypeTrait.php'],
            [__DIR__.'/_stubs/MissingReturnTypeClass.php'],
            [__DIR__.'/_stubs/MissingReturnTypeInterface.php'],
            [__DIR__.'/_stubs/MissingReturnTypeTrait.php'],
        ];
    }

    /**
     * @dataProvider improperFilesProvider
     */
    public function testImproperFile(string $path)
    {
        $checker = new Checker([$path]);

        $report = $checker->check();

        $this->assertTrue($report->hasIssues());
        $this->assertSame(1, $report->getNumberOfItems());
    }

    public function testClassInTheSameFileWithTrait()
    {
        $checker = new Checker([__DIR__.'/_stubs/ClassInTheSameFileWithTrait.php']);

        $report = $checker->check();

        $this->assertSame(3, $report->getNumberOfItems());
    }

    public function testSrcDirectory()
    {
        $checker = new Checker([__DIR__.'/../src']);
        $checker->skipReturnTypes();

        $this->assertFalse($checker->check()->hasIssues());
    }

    public function testExcludingNonExistentInstance()
    {
        $checker = new Checker([__DIR__.'/../src']);

        $this->expectException(\InvalidArgumentException::class);

        $checker->exclude('Nope\Nope\Nope');
    }

    public function excludingItselfProvider()
    {
        return [
            ['ProperClass'],
            ['ProperInterface'],
            ['ProperTrait'],
        ];
    }

    /**
     * @dataProvider excludingItselfProvider
     */
    public function testExcludingItself(string $class)
    {
        $checker = new Checker([__DIR__.'/_stubs/'.$class.'.php']);
        $checker->exclude('Tests\Stub\\'.$class);

        $this->assertSame(0, $checker->check()->getNumberOfItems());
    }

    public function testExcludingParentClass()
    {
        $checker = new Checker([__DIR__.'/_stubs/ChildClass.php']);
        $checker->exclude('Tests\Stub\MissingParameterTypeClass');

        $this->assertSame(0, $checker->check()->getNumberOfItems());
    }

    public function testExcludingInterface()
    {
        $checker = new Checker([__DIR__.'/_stubs/ProperClass.php']);
        $checker->exclude('Tests\Stub\ProperInterface');

        $this->assertSame(0, $checker->check()->getNumberOfItems());
    }

    public function testExcludingTrait()
    {
        $checker = new Checker([__DIR__.'/_stubs/ProperClass.php', __DIR__.'/_stubs/ProperTrait.php']);
        $checker->exclude('Tests\Stub\ProperTrait');

        $this->assertSame(1, $checker->check()->getNumberOfItems());
    }

    public function testExcludingTraitInTheSameFile()
    {
        $checker = new Checker([__DIR__.'/_stubs/ClassInTheSameFileWithTrait.php']);
        $checker->exclude('Tests\Stub\ClassInTheSameFileWithTrait');
        $checker->exclude('Tests\Stub\AnotherTrait');

        $this->assertSame(1, $checker->check()->getNumberOfItems());
    }

    public function testSkippingReturnTypes()
    {
        $checker = new Checker([
            __DIR__.'/_stubs/MissingReturnTypeClass.php',
            __DIR__.'/_stubs/MissingReturnTypeInterface.php',
            __DIR__.'/_stubs/MissingReturnTypeTrait.php',
        ]);
        $checker->skipReturnTypes();

        $report = $checker->check();

        $this->assertFalse($report->hasIssues());
        $this->assertSame(3, $report->getNumberOfItems());
    }

    /**
     * @coversNothing
     * @requires PHP 7.1
     */
    public function testPhp71Features()
    {
        $checker = new Checker([__DIR__.'/_stubs/Php71Class.php']);

        $this->assertFalse($checker->check()->hasIssues());
    }
}
