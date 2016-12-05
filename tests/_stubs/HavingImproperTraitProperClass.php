<?php

namespace Tests\Stub;

class HavingImproperTraitProperClass
{
    use MissingParameterTypeTrait;

    public function fine(): int
    {
        return 0;
    }
}
