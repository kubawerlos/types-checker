<?php

namespace Tests;

use KubaWerlos\TypesChecker\Report;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Report
 */
class ReportTest extends TestCase
{
    public function testEmpty()
    {
        $report = new Report();

        $this->assertTrue($report->isProper());
    }

    public function testSingleError()
    {
        $report = new Report();
        $report->addErrors('Foo', 'error');

        $this->assertFalse($report->isProper());
    }

    public function testThreeClasses()
    {
        $report = new Report();
        $report->addErrors('Foo', 'error');
        $report->addErrors('Bar', 'error');
        $report->addErrors('Baz', 'error');

        $this->assertCount(3, $report->getErrors());
    }
}
