<?php

namespace Tests;

use KubaWerlos\TypesChecker\ClassCollector;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\ClassCollector
 */
class ClassCollectorTest extends TestCase
{
    public function testWithIncorrectPath()
    {
        $this->expectException(\InvalidArgumentException::class);

        new ClassCollector([__DIR__.'/nope/nope/nope']);
    }

    public function testReadingDirectory()
    {
        $classCollector = new ClassCollector([__DIR__.'/../src']);

        $this->assertNotEmpty($classCollector->getClasses());
    }

    public function testReadingFile()
    {
        $classCollector = new ClassCollector([__DIR__.'/../src/Checker.php']);

        $this->assertCount(1, $classCollector->getClasses());
    }

    public function testReadingNonPsr4Class()
    {
        new ClassCollector([__DIR__.'/../tests/_stubs']);

        $this->assertTrue(class_exists('IForgotPsr4'));
    }
}
