<?php

namespace Tests;

use KubaWerlos\TypesChecker\Console\Application;
use KubaWerlos\TypesChecker\Console\Command;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \KubaWerlos\TypesChecker\Console\Application
 * @covers \KubaWerlos\TypesChecker\Console\Command
 */
class ConsoleTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;

    protected function setUp()
    {
        $application = new Application();

        $application->add(new Command());

        $command = $application->find('types-checker');
        $this->commandTester = new CommandTester($command);
    }

    public function testRun()
    {
        $this->commandTester->execute([
            'path' => ['src'],
        ]);

        $this->assertContains('missing return type', $this->commandTester->getDisplay());
    }

    public function testRunWithoutReturnTypes()
    {
        $this->commandTester->execute([
            'path' => ['src'],
            '--skip-return-types' => true,
        ]);

        $this->assertContains('nothing found', $this->commandTester->getDisplay());
    }

    public function testRunWithExcludedClass()
    {
        $this->commandTester->execute([
            'path' => [__DIR__.'/_stubs/MissingParameterTypeClass.php'],
            '--exclude' => ['Tests\Stub\MissingParameterTypeClass'],
        ]);

        $this->assertContains('0 items checked', $this->commandTester->getDisplay());
    }
}
