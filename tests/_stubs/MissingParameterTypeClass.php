<?php

namespace Tests\Stub;

class MissingParameterTypeClass implements DummyInterface
{
    public function test($x): bool
    {
    }
}
