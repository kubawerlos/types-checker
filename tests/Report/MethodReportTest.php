<?php

namespace Tests\Report;

use KubaWerlos\TypesChecker\Report\MethodReport;
use PHPUnit\Framework\TestCase;
use Tests\Stub\ProperClass;

/**
 * @covers \KubaWerlos\TypesChecker\Report\MethodReport
 */
class MethodReportTest extends TestCase
{
    public function testGettingName()
    {
        $report = new MethodReport(new \ReflectionMethod(ProperClass::class, 'test'));

        $this->assertSame('test', $report->getName());
    }

    public function testAddingIssue()
    {
        $report = new MethodReport(new \ReflectionMethod(ProperClass::class, 'test'));

        $report->addIssue('an issue');

        $this->assertCount(1, $report->getIssues());
    }
}
