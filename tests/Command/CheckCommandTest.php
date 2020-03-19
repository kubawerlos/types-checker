<?php

declare(strict_types = 1);

namespace Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;
use TypesChecker\Command\CheckCommand;

/**
 * @covers \TypesChecker\Command\CheckCommand
 *
 * @internal
 */
final class CheckCommandTest extends TestCase
{
    /** @var ApplicationTester */
    private $tester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $command = new CheckCommand();

        $application->add($command);

        $application->setDefaultCommand($command->getName(), true);

        $this->tester = new ApplicationTester($application);
    }

    public function testRunWithInvalidAutoloader(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectErrorMessage('File "nowhere" does not exist.');

        $this->tester->run([
            'path' => ['src'],
            '--autoloader' => 'nowhere',
        ]);
    }

    public function testRunWithUnknownClass(): void
    {
        $this->expectException(\Error::class);
        $this->expectErrorMessage("Class 'HiddenPlace\UnknownClass' not found");

        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ExtendingUnknownClass.php'],
        ]);
    }

    public function testRunWithAutoloaderForUnknownClass(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ExtendingUnknownClass.php'],
            '--autoloader' => __DIR__ . '/../_stubs/.HiddenPlace/autoloader.php',
        ]);
        static::assertStringContainsString('1 class', $this->tester->getDisplay());
    }

    public function testRun(): void
    {
        $this->tester->run([
            'path' => ['src'],
        ]);

        static::assertStringNotContainsString('missing return type', $this->tester->getDisplay());
    }

    public function testRunWithoutReturnTypes(): void
    {
        $this->tester->run([
            'path' => ['src'],
            '--skip-return-types' => true,
        ]);

        static::assertStringContainsString('No issues found', $this->tester->getDisplay());
    }

    public function testRunWithExcludedClass(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/MissingParameterTypeClass.php'],
            '--exclude' => ['Tests\Stub\MissingParameterTypeClass'],
        ]);

        static::assertStringContainsString('0 items checked', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneClass(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ProperClass.php'],
        ]);

        static::assertStringContainsString('1 class', $this->tester->getDisplay());
        static::assertStringNotContainsString('item', $this->tester->getDisplay());
        static::assertStringNotContainsString('classes', $this->tester->getDisplay());
        static::assertStringNotContainsString('interface', $this->tester->getDisplay());
        static::assertStringNotContainsString('trait', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneInterface(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ProperInterface.php'],
        ]);

        static::assertStringContainsString('1 interface', $this->tester->getDisplay());
        static::assertStringNotContainsString('item', $this->tester->getDisplay());
        static::assertStringNotContainsString('interfaces', $this->tester->getDisplay());
        static::assertStringNotContainsString('class', $this->tester->getDisplay());
        static::assertStringNotContainsString('trait', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneTrait(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ProperTrait.php'],
        ]);

        static::assertStringContainsString('1 trait', $this->tester->getDisplay());
        static::assertStringNotContainsString('item', $this->tester->getDisplay());
        static::assertStringNotContainsString('traits', $this->tester->getDisplay());
        static::assertStringNotContainsString('classes', $this->tester->getDisplay());
        static::assertStringNotContainsString('interface', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneClassAndInterface(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ProperClass.php', __DIR__ . '/../_stubs/ProperInterface.php'],
        ]);

        static::assertStringContainsString('2 items', $this->tester->getDisplay());
        static::assertStringContainsString('1 class', $this->tester->getDisplay());
        static::assertStringContainsString('1 interface', $this->tester->getDisplay());
        static::assertStringNotContainsString('classes', $this->tester->getDisplay());
        static::assertStringNotContainsString('interfaces', $this->tester->getDisplay());
        static::assertStringNotContainsString('trait', $this->tester->getDisplay());
    }

    public function testRunWithMissingType(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/MissingReturnTypeClass.php'],
        ]);

        static::assertStringContainsString('missing return type', $this->tester->getDisplay());
    }
}
