<?php

namespace Tests;

use KubaWerlos\TypesChecker\Report;
use PHPUnit\Framework\TestCase;
use Tests\Stub\ProperClass;
use Tests\Stub\ProperInterface;
use Tests\Stub\ProperTrait;

/**
 * @covers \KubaWerlos\TypesChecker\Report
 */
class ReportTest extends TestCase
{
    public function testEmpty()
    {
        $report = new Report();

        $this->assertTrue($report->isProper());
        $this->assertSame(0, $report->getItemsCount());
    }

    public function testSingleError()
    {
        $report = new Report();
        $report->addErrors(new \ReflectionMethod(ProperClass::class, 'test'), 'error');

        $this->assertFalse($report->isProper());
    }

    public function testThreeClasses()
    {
        $report = new Report();
        $report->addErrors(new \ReflectionMethod(ProperInterface::class, 'test'), 'error');
        $report->addErrors(new \ReflectionMethod(ProperTrait::class, 'test'), 'error');
        $report->addErrors(new \ReflectionMethod(ProperClass::class, 'test'), 'error');

        $this->assertInternalType('array', $report->getErrors());
        $this->assertCount(3, $report->getErrors());
        $this->assertArrayHasKey('Interface '.ProperInterface::class, $report->getErrors());
        $this->assertArrayHasKey('Trait '.ProperTrait::class, $report->getErrors());
        $this->assertArrayHasKey('Class '.ProperClass::class, $report->getErrors());
    }

    public function testItemsCount()
    {
        $report = new Report();
        $report->incrementItemsCount();
        $report->incrementItemsCount();
        $report->incrementItemsCount();

        $this->assertSame(3, $report->getItemsCount());
    }
}
