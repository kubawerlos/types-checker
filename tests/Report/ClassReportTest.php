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

        self::assertCount(0, $report->getMethods());
        self::assertSame(0, $report->getNumberOfIssues());
        self::assertFalse($report->hasIssues());
    }

    public function testAddingIssue(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperClass::class));

        $report->addIssue(new \ReflectionMethod(ProperClass::class, 'test'), 'an issue');

        self::assertTrue($report->hasIssues());
        self::assertSame(1, $report->getNumberOfIssues());
    }

    public function testBeingClasses(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperClass::class));

        self::assertSame('Class Tests\Stub\ProperClass', $report->getName());
        self::assertTrue($report->isClass());
        self::assertFalse($report->isInterface());
        self::assertFalse($report->isTrait());
    }

    public function testBeingInterfaces(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperInterface::class));

        self::assertSame('Interface Tests\Stub\ProperInterface', $report->getName());
        self::assertFalse($report->isClass());
        self::assertTrue($report->isInterface());
        self::assertFalse($report->isTrait());
    }

    public function testBeingTraits(): void
    {
        $report = new ClassReport(new \ReflectionClass(ProperTrait::class));

        self::assertSame('Trait Tests\Stub\ProperTrait', $report->getName());
        self::assertFalse($report->isClass());
        self::assertFalse($report->isInterface());
        self::assertTrue($report->isTrait());
    }
}
