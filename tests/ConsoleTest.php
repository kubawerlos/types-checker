<?php

namespace Tests;

use KubaWerlos\TypesChecker\Console\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @covers \KubaWerlos\TypesChecker\Console\Application
 * @covers \KubaWerlos\TypesChecker\Console\Command
 */
class ConsoleTest extends TestCase
{
    /** @var ApplicationTester */
    private $tester;

    protected function setUp()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $this->tester = new ApplicationTester($application);
    }

    public function testRun()
    {
        $this->tester->run([
            'path' => ['src'],
        ]);

        $this->assertContains('missing return type', $this->tester->getDisplay());
    }

    public function testRunWithoutReturnTypes()
    {
        $this->tester->run([
            'path' => ['src'],
            '--skip-return-types' => true,
        ]);

        $this->assertContains('Nothing found', $this->tester->getDisplay());
    }

    public function testRunWithExcludedClass()
    {
        $this->tester->run([
            'path' => [__DIR__.'/_stubs/MissingParameterTypeClass.php'],
            '--exclude' => ['Tests\Stub\MissingParameterTypeClass'],
        ]);

        $this->assertContains('0 items checked', $this->tester->getDisplay());
    }
}
