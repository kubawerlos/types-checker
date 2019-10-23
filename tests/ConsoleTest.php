<?php

declare(strict_types = 1);

namespace Tests;

use KubaWerlos\TypesChecker\Console\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @covers \KubaWerlos\TypesChecker\Console\Application
 * @covers \KubaWerlos\TypesChecker\Console\Command
 *
 * @internal
 */
final class ConsoleTest extends TestCase
{
    /** @var ApplicationTester */
    private $tester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $this->tester = new ApplicationTester($application);
    }

    public function testRun(): void
    {
        $this->tester->run([
            'path' => ['src'],
        ]);

        static::assertContains('missing return type', $this->tester->getDisplay());
    }

    public function testRunWithoutReturnTypes(): void
    {
        $this->tester->run([
            'path' => ['src'],
            '--skip-return-types' => true,
        ]);

        static::assertContains('No issues found', $this->tester->getDisplay());
    }

    public function testRunWithExcludedClass(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/_stubs/MissingParameterTypeClass.php'],
            '--exclude' => ['Tests\Stub\MissingParameterTypeClass'],
        ]);

        static::assertContains('0 items checked', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneClass(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/_stubs/ProperClass.php'],
        ]);

        static::assertContains('1 class', $this->tester->getDisplay());
        static::assertNotContains('item', $this->tester->getDisplay());
        static::assertNotContains('classes', $this->tester->getDisplay());
        static::assertNotContains('interface', $this->tester->getDisplay());
        static::assertNotContains('trait', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneInterface(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/_stubs/ProperInterface.php'],
        ]);

        static::assertContains('1 interface', $this->tester->getDisplay());
        static::assertNotContains('item', $this->tester->getDisplay());
        static::assertNotContains('interfaces', $this->tester->getDisplay());
        static::assertNotContains('class', $this->tester->getDisplay());
        static::assertNotContains('trait', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneTrait(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/_stubs/ProperTrait.php'],
        ]);

        static::assertContains('1 trait', $this->tester->getDisplay());
        static::assertNotContains('item', $this->tester->getDisplay());
        static::assertNotContains('traits', $this->tester->getDisplay());
        static::assertNotContains('classes', $this->tester->getDisplay());
        static::assertNotContains('interface', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneClassAndInterface(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/_stubs/ProperClass.php', __DIR__ . '/_stubs/ProperInterface.php'],
        ]);

        static::assertContains('2 items', $this->tester->getDisplay());
        static::assertContains('1 class', $this->tester->getDisplay());
        static::assertContains('1 interface', $this->tester->getDisplay());
        static::assertNotContains('classes', $this->tester->getDisplay());
        static::assertNotContains('interfaces', $this->tester->getDisplay());
        static::assertNotContains('trait', $this->tester->getDisplay());
    }
}
