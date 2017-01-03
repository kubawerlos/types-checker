<?php

namespace Tests\Report;

use KubaWerlos\TypesChecker\Report\Report;
use PHPUnit\Framework\TestCase;
use Tests\Stub\ProperClass;
use Tests\Stub\ProperInterface;
use Tests\Stub\ProperTrait;

/**
 * @covers \KubaWerlos\TypesChecker\Report\Report
 */
class ReportTest extends TestCase
{
    public function testMultipleMethodAdding()
    {
        $report = new Report();

        $report->addIssue(new \ReflectionMethod(ProperClass::class, 'test'), 'first issue');
        $report->addIssue(new \ReflectionMethod(ProperClass::class, 'test'), 'second issue');

        $this->assertCount(1, $report->getClasses());
        $this->assertSame(1, $report->getNumberOfItems());
        $this->assertSame(2, $report->getNumberOfIssues());
        $this->assertTrue($report->hasIssues());
    }

    public function testCountingClasses()
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperClass::class));

        $this->assertSame(1, $report->getNumberOfClasses());
        $this->assertSame(0, $report->getNumberOfInterfaces());
        $this->assertSame(0, $report->getNumberOfTraits());
        $this->assertFalse($report->hasIssues());
    }

    public function testCountingInterfaces()
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperInterface::class));

        $this->assertSame(0, $report->getNumberOfClasses());
        $this->assertSame(1, $report->getNumberOfInterfaces());
        $this->assertSame(0, $report->getNumberOfTraits());
        $this->assertFalse($report->hasIssues());
    }

    public function testCountingTraits()
    {
        $report = new Report();

        $report->addClass(new \ReflectionClass(ProperTrait::class));

        $this->assertSame(0, $report->getNumberOfClasses());
        $this->assertSame(0, $report->getNumberOfInterfaces());
        $this->assertSame(1, $report->getNumberOfTraits());
        $this->assertFalse($report->hasIssues());
    }
}
