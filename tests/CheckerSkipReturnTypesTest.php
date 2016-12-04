<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Checker
 */
class CheckerSkipReturnTypesTest extends TestCase
{
    public function testSkippingReturnTypes()
    {
        $checker = new Checker([
            __DIR__.'/_stubs/MissingReturnTypeClass.php',
            __DIR__.'/_stubs/MissingReturnTypeInterface.php',
            __DIR__.'/_stubs/MissingReturnTypeTrait.php',
        ]);
        $checker->skipReturnTypes();

        $report = $checker->check();

        $this->assertTrue($report->isProper());
        $this->assertSame(3, $report->getItemsCount());
    }
}
