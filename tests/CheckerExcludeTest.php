<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KubaWerlos\TypesChecker\Checker::exclude
 * @covers \KubaWerlos\TypesChecker\Checker::<private>
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
            ['ProperClass'],
            ['ProperInterface'],
            ['ProperTrait'],
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

    public function testExcludingParentClass()
    {
        $checker = new Checker([__DIR__.'/_stubs/ChildClass.php']);
        $checker->exclude('Tests\Stub\MissingParameterTypeClass');

        $this->assertSame(0, $checker->check()->getItemsCount());
    }

    public function testExcludingInterface()
    {
        $checker = new Checker([__DIR__.'/_stubs/ProperClass.php']);
        $checker->exclude('Tests\Stub\ProperInterface');

        $this->assertSame(0, $checker->check()->getItemsCount());
    }

    public function testExcludingTrait()
    {
        $checker = new Checker([__DIR__.'/_stubs/ProperClass.php', __DIR__.'/_stubs/ProperTrait.php']);
        $checker->exclude('Tests\Stub\ProperTrait');

        $this->assertSame(1, $checker->check()->getItemsCount());
    }

    public function testExcludingTraitInTheSameFile()
    {
        $checker = new Checker([__DIR__.'/_stubs/ClassInTheSameFileWithTrait.php']);
        $checker->exclude('Tests\Stub\ClassInTheSameFileWithTrait');
        $checker->exclude('Tests\Stub\AnotherTrait');

        $this->assertSame(1, $checker->check()->getItemsCount());
    }
}
