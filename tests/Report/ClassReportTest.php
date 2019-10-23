<?php

declare(strict_types = 1);

namespace Tests\Report;

use PHPUnit\Framework\TestCase;
use Tests\Stub\ProperClass;
use Tests\Stub\ProperInterface;
use Tests\Stub\ProperTrait;
use TypesChecker\Report\ClassReport;

/**
 * @covers \TypesChecker\Report\ClassReport
 *
 * @internal
 */
final class ClassReportTest extends TestCase
{
    public function testEmptyReport(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperClass::class));

        static::assertCount(0, $report->getMethods());
        static::assertSame(0, $report->getNumberOfIssues());
        static::assertFalse($report->hasIssues());
    }

    public function testAddingIssue(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperClass::class));

        $report->addIssue(new \ReflectionMethod(ProperClass::class, 'test'), 'an issue');

        static::assertTrue($report->hasIssues());
        static::assertSame(1, $report->getNumberOfIssues());
    }

    public function testBeingClasses(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperClass::class));

        static::assertSame('Class Tests\Stub\ProperClass', $report->getName());
        static::assertTrue($report->isClass());
        static::assertFalse($report->isInterface());
        static::assertFalse($report->isTrait());
    }

    public function testBeingInterfaces(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperInterface::class));

        static::assertSame('Interface Tests\Stub\ProperInterface', $report->getName());
        static::assertFalse($report->isClass());
        static::assertTrue($report->isInterface());
        static::assertFalse($report->isTrait());
    }

    public function testBeingTraits(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperTrait::class));

        static::assertSame('Trait Tests\Stub\ProperTrait', $report->getName());
        static::assertFalse($report->isClass());
        static::assertFalse($report->isInterface());
        static::assertTrue($report->isTrait());
    }
}
