<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Checker
 */
class CheckerExcludeTest extends TestCase
{
    public function testExcludingNonExistentInstance()
    {
        $checker = new Checker([__DIR__.'/../src']);

        $this->expectException(\InvalidArgumentException::class);

        $checker->exclude('Nope\Nope\Nope');
    }

    public function excludingItselfProvider()
    {
        return [
            ['CorrectClass'],
            ['CorrectInterface'],
            ['CorrectTrait'],
        ];
    }

    /**
     * @dataProvider excludingItselfProvider
     */
    public function testExcludingItself(string $class)
    {
        $checker = new Checker([__DIR__.'/_stubs/'.$class.'.php']);
        $checker->exclude('Tests\Stub\\'.$class);

        $this->assertSame(0, $checker->check()->getItemsCount());
    }
}
