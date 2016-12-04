<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Checker
 */
class CheckerPathTest extends TestCase
{
    public function testWithIncorrectPath()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Checker([__DIR__.'/nope/nope/nope']);
    }

    public function correctFileProvider()
    {
        return [
            [__DIR__.'/_stubs/CorrectClass.php'],
            [__DIR__.'/_stubs/CorrectInterface.php'],
            [__DIR__.'/_stubs/CorrectTrait.php'],
        ];
    }

    /**
     * @dataProvider correctFileProvider
     */
    public function testCorrectFile(string $path)
    {
        $checker = new Checker([$path]);

        $report = $checker->check();

        $this->assertTrue($report->isProper());
        $this->assertSame(1, $report->getItemsCount());
    }

    public function incorrectFileProvider()
    {
        return [
            [__DIR__.'/_stubs/MissingParameterTypeClass.php'],
            [__DIR__.'/_stubs/MissingParameterTypeInterface.php'],
            [__DIR__.'/_stubs/MissingParameterTypeTrait.php'],
            [__DIR__.'/_stubs/MissingReturnTypeClass.php'],
            [__DIR__.'/_stubs/MissingReturnTypeInterface.php'],
            [__DIR__.'/_stubs/MissingReturnTypeTrait.php'],
            [__DIR__.'/_stubs/UsingMissingParameterTypeTraitClass.php'],
        ];
    }

    /**
     * @dataProvider incorrectFileProvider
     */
    public function testIncorrectFile(string $path)
    {
        $checker = new Checker([$path]);

        $report = $checker->check();

        $this->assertFalse($report->isProper());
        $this->assertSame(1, $report->getItemsCount());
    }
}
