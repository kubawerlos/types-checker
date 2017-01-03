<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Checker::__construct
 * @covers \KubaWerlos\TypesChecker\Checker::check
 * @covers \KubaWerlos\TypesChecker\Checker::<private>
 */
class CheckerPathTest extends TestCase
{
    public function testWithIncorrectPath()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Checker([__DIR__.'/nope/nope/nope']);
    }

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
}
