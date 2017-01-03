<?php

namespace Tests\Report;

use KubaWerlos\TypesChecker\Report\ClassReport;
use PHPUnit\Framework\TestCase;
use Tests\Stub\ProperClass;
use Tests\Stub\ProperInterface;
use Tests\Stub\ProperTrait;

/**
 * @covers \KubaWerlos\TypesChecker\Report\ClassReport
 */
class ClassReportTest extends TestCase
{
    public function testEmptyReport()
    {
        $report = new ClassReport(new \ReflectionClass(ProperClass::class));

        $this->assertCount(0, $report->getMethods());
        $this->assertFalse($report->hasIssues());
    }

    public function testAddingIssue()
    {
        $report = new ClassReport(new \ReflectionClass(ProperClass::class));

        $report->addIssue(new \ReflectionMethod(ProperClass::class, 'test'), 'an issue');

        $this->assertTrue($report->hasIssues());
    }

    public function testBeingClasses()
    {
        $report = new ClassReport(new \ReflectionClass(ProperClass::class));

        $this->assertSame('Class Tests\Stub\ProperClass', $report->getName());
        $this->assertTrue($report->isClass());
        $this->assertFalse($report->isInterface());
        $this->assertFalse($report->isTrait());
    }

    public function testBeingInterfaces()
    {
        $report = new ClassReport(new \ReflectionClass(ProperInterface::class));

        $this->assertSame('Interface Tests\Stub\ProperInterface', $report->getName());
        $this->assertFalse($report->isClass());
        $this->assertTrue($report->isInterface());
        $this->assertFalse($report->isTrait());
    }

    public function testBeingTraits()
    {
        $report = new ClassReport(new \ReflectionClass(ProperTrait::class));

        $this->assertSame('Trait Tests\Stub\ProperTrait', $report->getName());
        $this->assertFalse($report->isClass());
        $this->assertFalse($report->isInterface());
        $this->assertTrue($report->isTrait());
    }
}
