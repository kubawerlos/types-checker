<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use TypesChecker\Checker;

/**
 * @covers \TypesChecker\Checker
 *
 * @internal
 */
final class CheckerTest extends TestCase
{
    public static function provideProperFileCases(): iterable
    {
        return [
            [__DIR__ . '/_stubs/ChildClass.php'],
            [__DIR__ . '/_stubs/ProperClass.php'],
            [__DIR__ . '/_stubs/ProperInterface.php'],
            [__DIR__ . '/_stubs/ProperTrait.php'],
            [__DIR__ . '/_stubs/HavingImproperTraitProperClass.php'],
            [__DIR__ . '/_stubs/HavingImproperTraitProperOverridingClass.php'],
        ];
    }

    /**
     * @dataProvider provideProperFileCases
     */
    public function testProperFile(string $path): void
    {
        $checker = new Checker([$path]);

        $report = $checker->check();

        self::assertFalse($report->hasIssues());
        self::assertSame(1, $report->getNumberOfItems());
    }

    public static function provideImproperFileCases(): iterable
    {
        return [
            [__DIR__ . '/_stubs/HavingProperTraitImproperClass.php'],
            [__DIR__ . '/_stubs/HavingProperTraitImproperOverridingClass.php'],
            [__DIR__ . '/_stubs/MissingParameterTypeClass.php'],
            [__DIR__ . '/_stubs/MissingParameterTypeInterface.php'],
            [__DIR__ . '/_stubs/MissingParameterTypeTrait.php'],
            [__DIR__ . '/_stubs/MissingReturnTypeClass.php'],
            [__DIR__ . '/_stubs/MissingReturnTypeInterface.php'],
            [__DIR__ . '/_stubs/MissingReturnTypeTrait.php'],
        ];
    }

    /**
     * @dataProvider provideImproperFileCases
     */
    public function testImproperFile(string $path): void
    {
        $checker = new Checker([$path]);

        $report = $checker->check();

        self::assertTrue($report->hasIssues());
        self::assertSame(1, $report->getNumberOfItems());
    }

    public function testClassInTheSameFileWithTrait(): void
    {
        $checker = new Checker([__DIR__ . '/_stubs/ClassInTheSameFileWithTrait.php']);

        $report = $checker->check();

        self::assertSame(3, $report->getNumberOfItems());
    }

    public function testSrcDirectory(): void
    {
        $checker = new Checker([__DIR__ . '/../src']);
        $checker->skipReturnTypes();

        self::assertFalse($checker->check()->hasIssues());
    }

    public function testExcludingNonExistentInstance(): void
    {
        $checker = new Checker([__DIR__ . '/../src']);

        $this->expectException(\InvalidArgumentException::class);

        $checker->exclude('Nope\Nope\Nope');
    }

    public static function provideExcludingItselfCases(): iterable
    {
        return [
            ['ProperClass'],
            ['ProperInterface'],
            ['ProperTrait'],
        ];
    }

    /**
     * @dataProvider provideExcludingItselfCases
     */
    public function testExcludingItself(string $class): void
    {
        $checker = new Checker([__DIR__ . '/_stubs/' . $class . '.php']);
        $checker->exclude('Tests\Stub\\' . $class);

        self::assertSame(0, $checker->check()->getNumberOfItems());
    }

    public function testExcludingParentClass(): void
    {
        $checker = new Checker([__DIR__ . '/_stubs/ChildClass.php']);
        $checker->exclude('Tests\Stub\MissingParameterTypeClass');

        self::assertSame(0, $checker->check()->getNumberOfItems());
    }

    public function testExcludingInterface(): void
    {
        $checker = new Checker([__DIR__ . '/_stubs/ProperClass.php']);
        $checker->exclude('Tests\Stub\ProperInterface');

        self::assertSame(0, $checker->check()->getNumberOfItems());
    }

    public function testExcludingTrait(): void
    {
        $checker = new Checker([__DIR__ . '/_stubs/ProperClass.php', __DIR__ . '/_stubs/ProperTrait.php']);
        $checker->exclude('Tests\Stub\ProperTrait');

        self::assertSame(1, $checker->check()->getNumberOfItems());
    }

    public function testExcludingTraitInTheSameFile(): void
    {
        $checker = new Checker([__DIR__ . '/_stubs/ClassInTheSameFileWithTrait.php']);
        $checker->exclude('Tests\Stub\ClassInTheSameFileWithTrait');
        $checker->exclude('Tests\Stub\AnotherTrait');

        self::assertSame(1, $checker->check()->getNumberOfItems());
    }

    public function testSkippingReturnTypes(): void
    {
        $checker = new Checker([
            __DIR__ . '/_stubs/MissingReturnTypeClass.php',
            __DIR__ . '/_stubs/MissingReturnTypeInterface.php',
            __DIR__ . '/_stubs/MissingReturnTypeTrait.php',
        ]);
        $checker->skipReturnTypes();

        $report = $checker->check();

        self::assertFalse($report->hasIssues());
        self::assertSame(3, $report->getNumberOfItems());
    }

    /**
     * @coversNothing
     * @requires PHP 7.1
     */
    public function testPhp71Features(): void
    {
        $checker = new Checker([__DIR__ . '/_stubs/Php71Class.php']);

        self::assertFalse($checker->check()->hasIssues());
    }
}
