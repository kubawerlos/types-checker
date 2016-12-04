<?php

namespace Tests\Stub;

class MissingParameterType implements DummyInterface
{
    public function test($x): bool
    {
    }
}
