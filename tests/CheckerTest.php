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

        new Checker(__DIR__.'/nope/nope/nope');
    }

    public function testSrcDirectory()
    {
        $checker = new Checker(__DIR__.'/../src');
        $checker->skipReturnTypes();

        $this->assertTrue($checker->check()->isProper());
    }

    public function testCorrectClass()
    {
        $checker = new Checker(__DIR__.'/_stubs/Correct.php');

        $this->assertTrue($checker->check()->isProper());
    }

    public function testMissingParameterTypeClass()
    {
        $checker = new Checker(__DIR__.'/_stubs/MissingParameterType.php');

        $this->assertFalse($checker->check()->isProper());
    }
}
