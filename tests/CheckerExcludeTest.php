<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Checker
 */
class CheckerExcludeTest extends TestCase
{
    public function testSkippingReturnTypes()
    {
        $checker = new Checker([__DIR__.'/_stubs/MissingReturnTypeClass.php']);
        $checker->skipReturnTypes();

        $this->assertTrue($checker->check()->isProper());
    }

    public function testExcludingNonExistentInstance()
    {
        $checker = new Checker([__DIR__.'/../src']);

        $this->expectException(\InvalidArgumentException::class);

        $checker->exclude('Nope\Nope\Nope');
    }

    public function testExcludingClass()
    {
        $checker = new Checker([__DIR__.'/_stubs/CorrectClass.php']);
        $checker->exclude('Tests\Stub\CorrectClass');

        $this->assertSame(0, $checker->check()->getItemsCount());
    }

    public function testExcludingInterface()
    {
        $checker = new Checker([__DIR__.'/_stubs/CorrectInterface.php']);
        $checker->exclude('Tests\Stub\CorrectInterface');

        $this->assertSame(0, $checker->check()->getItemsCount());
    }
}
