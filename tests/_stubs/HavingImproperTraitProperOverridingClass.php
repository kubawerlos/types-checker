<?php

namespace Tests\Stub;

class HavingImproperTraitProperOverridingClass
{
    use MissingParameterTypeTrait;

    public function test(int $n): bool
    {
    }
}
