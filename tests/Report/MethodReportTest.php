<?php

declare(strict_types = 1);

namespace Tests\Report;

use KubaWerlos\TypesChecker\Report\MethodReport;
use PHPUnit\Framework\TestCase;
use Tests\Stub\ProperClass;

/**
 * @covers \KubaWerlos\TypesChecker\Report\MethodReport
 *
 * @internal
 */
final class MethodReportTest extends TestCase
{
    public function testGettingName(): void
    {
        $report = new MethodReport(new \ReflectionMethod(ProperClass::class, 'test'));

        static::assertSame('test', $report->getName());
    }

    public function testAddingIssue(): void
    {
        $report = new MethodReport(new \ReflectionMethod(ProperClass::class, 'test'));

        $report->addIssue('an issue');

        static::assertCount(1, $report->getIssues());
    }
}
