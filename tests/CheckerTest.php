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
        $checker = new Checker([__DIR__.'/../src']);
        $checker->skipReturnTypes();

        $this->assertTrue($checker->check()->isProper());
    }

    public function testExcludingNonExistentInstance()
    {
        $checker = new Checker([__DIR__.'/../src']);

        $this->expectException(\InvalidArgumentException::class);

        $checker->excludeInstance('Nope\Nope\Nope');
    }

    public function testExcludingClass()
    {
        $checker = new Checker([__DIR__.'/_stubs']);
        $checker->excludeInstance('Tests\Stub\MissingParameterTypeClass');
        $checker->excludeInstance('Tests\Stub\MissingReturnTypeClass');

        $this->assertTrue($checker->check()->isProper());
    }

    public function testExcludingInterface()
    {
        $checker = new Checker([__DIR__.'/_stubs']);
        $checker->excludeInstance('Tests\Stub\DummyInterface');

        $this->assertTrue($checker->check()->isProper());
    }
}
