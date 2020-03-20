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

        self::assertNotEmpty($classCollector->getClasses());
    }

    public function testReadingFile(): void
    {
        $classCollector = new ClassCollector([__DIR__ . '/../src/Checker.php']);

        self::assertCount(1, $classCollector->getClasses());
    }

    public function testReadingNonPsr4Class(): void
    {
        new ClassCollector([__DIR__ . '/../tests/_stubs']);

        self::assertTrue(\class_exists('Tests\Stub\IForgotPsr4'));
    }

    public function testClassWithWhitespaces(): void
    {
        $classCollector = new ClassCollector([__DIR__ . '/../tests/_stubs/WhitespacesOverdose.php']);

        self::assertSame(['Tests\\Stub\\WhitespacesOverdose'], $classCollector->getClasses());
    }
}
