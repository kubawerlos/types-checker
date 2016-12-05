<?php

namespace Tests\Stub;

trait ClassInTheSameFileWithTrait
{
    public function test($x)
    {
    }
}

class TraitIntTheSameFileClass
{
    use ClassInTheSameFileWithTrait;
    use AnotherTrait;
}

trait AnotherTrait
{
    public function anotherTest($x)
    {
    }
}
