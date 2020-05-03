<?php

declare(strict_types=1);

/*
 * This file is part of Types checker.
 *
 * (c) Kuba Werłos <werlos@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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

        self::assertCount(1, $report->getClasses());
        self::assertSame(1, $report->getNumberOfItems());
        self::assertSame(2, $report->getNumberOfIssues());
        self::assertTrue($report->hasIssues());
    }

    public function testCountingClasses(): void
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperClass::class));

        self::assertSame(1, $report->getNumberOfClasses());
        self::assertSame(0, $report->getNumberOfInterfaces());
        self::assertSame(0, $report->getNumberOfTraits());
        self::assertFalse($report->hasIssues());
    }

    public function testCountingInterfaces(): void
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperInterface::class));

        self::assertSame(0, $report->getNumberOfClasses());
        self::assertSame(1, $report->getNumberOfInterfaces());
        self::assertSame(0, $report->getNumberOfTraits());
        self::assertFalse($report->hasIssues());
    }

    public function testCountingTraits(): void
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperTrait::class));

        self::assertSame(0, $report->getNumberOfClasses());
        self::assertSame(0, $report->getNumberOfInterfaces());
        self::assertSame(1, $report->getNumberOfTraits());
        self::assertFalse($report->hasIssues());
    }
}
