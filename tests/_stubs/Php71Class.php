<?php

namespace Tests\Stub;

class Php71Class
{
    public function nullableParameters(?bool $b, ?float $f, ?int $i, ?string $s): bool
    {
    }

    public function nullableReturn(): ?Php71Class
    {
    }

    public function nullableReturnSelf(): ?self
    {
    }

    public function voidReturn(): void
    {
    }
}
