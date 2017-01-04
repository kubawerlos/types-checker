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

        $this->assertContains('No issues found', $this->tester->getDisplay());
    }

    public function testRunWithExcludedClass()
    {
        $this->tester->run([
            'path' => [__DIR__.'/_stubs/MissingParameterTypeClass.php'],
            '--exclude' => ['Tests\Stub\MissingParameterTypeClass'],
        ]);

        $this->assertContains('0 items checked', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneClass()
    {
        $this->tester->run([
            'path' => [__DIR__.'/_stubs/ProperClass.php'],
        ]);

        $this->assertContains('1 class', $this->tester->getDisplay());
        $this->assertNotContains('item', $this->tester->getDisplay());
        $this->assertNotContains('classes', $this->tester->getDisplay());
        $this->assertNotContains('interface', $this->tester->getDisplay());
        $this->assertNotContains('trait', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneInterface()
    {
        $this->tester->run([
            'path' => [__DIR__.'/_stubs/ProperInterface.php'],
        ]);

        $this->assertContains('1 interface', $this->tester->getDisplay());
        $this->assertNotContains('item', $this->tester->getDisplay());
        $this->assertNotContains('interfaces', $this->tester->getDisplay());
        $this->assertNotContains('class', $this->tester->getDisplay());
        $this->assertNotContains('trait', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneTrait()
    {
        $this->tester->run([
            'path' => [__DIR__.'/_stubs/ProperTrait.php'],
        ]);

        $this->assertContains('1 trait', $this->tester->getDisplay());
        $this->assertNotContains('item', $this->tester->getDisplay());
        $this->assertNotContains('traits', $this->tester->getDisplay());
        $this->assertNotContains('classes', $this->tester->getDisplay());
        $this->assertNotContains('interface', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneClassAndInterface()
    {
        $this->tester->run([
            'path' => [__DIR__.'/_stubs/ProperClass.php', __DIR__.'/_stubs/ProperInterface.php'],
        ]);

        $this->assertContains('2 items', $this->tester->getDisplay());
        $this->assertContains('1 class', $this->tester->getDisplay());
        $this->assertContains('1 interface', $this->tester->getDisplay());
        $this->assertNotContains('classes', $this->tester->getDisplay());
        $this->assertNotContains('interfaces', $this->tester->getDisplay());
        $this->assertNotContains('trait', $this->tester->getDisplay());
    }
}
