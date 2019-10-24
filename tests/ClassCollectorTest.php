<?php

declare(strict_types = 1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use TypesChecker\ClassCollector;

/**
 * @covers \TypesChecker\ClassCollector
 *
 * @internal
 */
final class ClassCollectorTest extends TestCase
{
    public function testWithIncorrectPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ClassCollector([__DIR__ . '/nope/nope/nope']);
    }

    public function testReadingDirectory(): void
    {
        $classCollector = new ClassCollector([__DIR__ . '/../src']);

        static::assertNotEmpty($classCollector->getClasses());
    }

    public function testReadingFile(): void
    {
        $classCollector = new ClassCollector([__DIR__ . '/../src/Checker.php']);

        static::assertCount(1, $classCollector->getClasses());
    }

    public function testReadingNonPsr4Class(): void
    {
        new ClassCollector([__DIR__ . '/../tests/_stubs']);

        static::assertTrue(\class_exists('Tests\Stub\IForgotPsr4'));
    }

    public function testClassWithWhitespaces(): void
    {
        $classCollector = new ClassCollector([__DIR__ . '/../tests/_stubs/WhitespacesOverdose.php']);

        static::assertSame(['Tests\\Stub\\WhitespacesOverdose'], $classCollector->getClasses());
    }
}
