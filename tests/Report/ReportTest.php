<?php

declare(strict_types = 1);

namespace Tests\Report;

use PHPUnit\Framework\TestCase;
use Tests\Stub\ProperClass;
use Tests\Stub\ProperInterface;
use Tests\Stub\ProperTrait;
use TypesChecker\Report\Report;

/**
 * @covers \TypesChecker\Report\Report
 *
 * @internal
 */
final class ReportTest extends TestCase
{
    public function testMultipleMethodAdding(): void
    {
        $report = new Report();

        $report->addIssue(new \ReflectionMethod(ProperClass::class, 'test'), 'first issue');
        $report->addIssue(new \ReflectionMethod(ProperClass::class, 'test'), 'second issue');

        static::assertCount(1, $report->getClasses());
        static::assertSame(1, $report->getNumberOfItems());
        static::assertSame(2, $report->getNumberOfIssues());
        static::assertTrue($report->hasIssues());
    }

    public function testCountingClasses(): void
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperClass::class));

        static::assertSame(1, $report->getNumberOfClasses());
        static::assertSame(0, $report->getNumberOfInterfaces());
        static::assertSame(0, $report->getNumberOfTraits());
        static::assertFalse($report->hasIssues());
    }

    public function testCountingInterfaces(): void
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperInterface::class));

        static::assertSame(0, $report->getNumberOfClasses());
        static::assertSame(1, $report->getNumberOfInterfaces());
        static::assertSame(0, $report->getNumberOfTraits());
        static::assertFalse($report->hasIssues());
    }

    public function testCountingTraits(): void
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperTrait::class));

        static::assertSame(0, $report->getNumberOfClasses());
        static::assertSame(0, $report->getNumberOfInterfaces());
        static::assertSame(1, $report->getNumberOfTraits());
        static::assertFalse($report->hasIssues());
    }
}
