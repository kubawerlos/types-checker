<?php

namespace Tests;

use KubaWerlos\TypesChecker\Checker;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 * @requires PHP 7.1
 */
class Php71Test extends TestCase
{
    public function testPhp71Features()
    {
        $checker = new Checker([__DIR__.'/_stubs/Php71Class.php']);

        $this->assertFalse($checker->check()->hasIssues());
    }
}
