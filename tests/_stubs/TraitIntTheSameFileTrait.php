<?php

namespace Tests\Stub;

trait TraitIntTheSameFileTrait
{
    public function test($x)
    {
    }
}

class TraitIntTheSameFileClass
{
    use TraitIntTheSameFileTrait;
}
