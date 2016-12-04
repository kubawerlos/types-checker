<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Checker
 */
class CheckerTest extends TestCase
{
    public function testWithIncorrectPath()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Checker([__DIR__.'/nope/nope/nope']);
    }

    public function testCorrectClass()
    {
        $checker = new Checker([__DIR__.'/_stubs/CorrectClass.php']);

        $this->assertTrue($checker->check()->isProper());
    }

    public function testMissingParameterTypeClass()
    {
        $checker = new Checker([__DIR__.'/_stubs/MissingParameterTypeClass.php']);

        $this->assertFalse($checker->check()->isProper());
    }

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
        $checker = new Checker([__DIR__.'/_stubs']);
        $checker->exclude('Tests\Stub\MissingParameterTypeClass');
        $checker->exclude('Tests\Stub\MissingReturnTypeClass');

        $this->assertTrue($checker->check()->isProper());
    }

    public function testExcludingInterface()
    {
        $checker = new Checker([__DIR__.'/_stubs']);
        $checker->exclude('Tests\Stub\DummyInterface');

        $this->assertTrue($checker->check()->isProper());
    }
}
