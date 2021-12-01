<?php declare(strict_types=1);

/*
 * This file is part of Types checker.
 *
 * (c) 2016 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Report;

use PHPUnit\Framework\TestCase;
use Tests\Stub\ProperClass;
use TypesChecker\Report\MethodReport;

/**
 * @covers \TypesChecker\Report\MethodReport
 *
 * @internal
 */
final class MethodReportTest extends TestCase
{
    public function testGettingName(): void
    {
        $report = new MethodReport(new \ReflectionMethod(ProperClass::class, 'test'));

        self::assertSame('test', $report->getName());
    }

    public function testAddingIssue(): void
    {
        $report = new MethodReport(new \ReflectionMethod(ProperClass::class, 'test'));

        $report->addIssue('an issue');

        self::assertCount(1, $report->getIssues());
    }
}
