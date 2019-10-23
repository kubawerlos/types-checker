<?php

declare(strict_types = 1);

namespace Tests;

use KubaWerlos\TypesChecker\ClassCollector;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\ClassCollector
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
}
