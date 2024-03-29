<?php declare(strict_types=1);

/*
 * This file is part of Types checker.
 *
 * (c) 2016 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
        $this->expectExceptionMessage('File "nowhere" does not exist.');

        $this->tester->run([
            'path' => ['src'],
            '--autoloader' => 'nowhere',
        ]);
    }

    /**
     * @requires PHP ^7.3
     */
    public function testRunWithUnknownClass(): void
    {
        $this->expectException(\Error::class);
        $this->expectErrorMessage("Class 'HiddenPlace\\UnknownClass' not found");

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
        self::assertStringContainsString('1 class', $this->tester->getDisplay());
    }

    public function testRun(): void
    {
        $this->tester->run([
            'path' => ['src'],
        ]);

        self::assertStringNotContainsString('missing return type', $this->tester->getDisplay());
    }

    public function testRunWithoutReturnTypes(): void
    {
        $this->tester->run([
            'path' => ['src'],
            '--skip-return-types' => true,
        ]);

        self::assertStringContainsString('No issues found', $this->tester->getDisplay());
    }

    public function testRunWithExcludedClass(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/MissingParameterTypeClass.php'],
            '--exclude' => ['Tests\Stub\MissingParameterTypeClass'],
        ]);

        self::assertStringContainsString('0 items checked', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneClass(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ProperClass.php'],
        ]);

        self::assertStringContainsString('1 class', $this->tester->getDisplay());
        self::assertStringNotContainsString('item', $this->tester->getDisplay());
        self::assertStringNotContainsString('classes', $this->tester->getDisplay());
        self::assertStringNotContainsString('interface', $this->tester->getDisplay());
        self::assertStringNotContainsString('trait', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneInterface(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ProperInterface.php'],
        ]);

        self::assertStringContainsString('1 interface', $this->tester->getDisplay());
        self::assertStringNotContainsString('item', $this->tester->getDisplay());
        self::assertStringNotContainsString('interfaces', $this->tester->getDisplay());
        self::assertStringNotContainsString('class', $this->tester->getDisplay());
        self::assertStringNotContainsString('trait', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneTrait(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ProperTrait.php'],
        ]);

        self::assertStringContainsString('1 trait', $this->tester->getDisplay());
        self::assertStringNotContainsString('item', $this->tester->getDisplay());
        self::assertStringNotContainsString('traits', $this->tester->getDisplay());
        self::assertStringNotContainsString('classes', $this->tester->getDisplay());
        self::assertStringNotContainsString('interface', $this->tester->getDisplay());
    }

    public function testRunOnlyForOneClassAndInterface(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/ProperClass.php', __DIR__ . '/../_stubs/ProperInterface.php'],
        ]);

        self::assertStringContainsString('2 items', $this->tester->getDisplay());
        self::assertStringContainsString('1 class', $this->tester->getDisplay());
        self::assertStringContainsString('1 interface', $this->tester->getDisplay());
        self::assertStringNotContainsString('classes', $this->tester->getDisplay());
        self::assertStringNotContainsString('interfaces', $this->tester->getDisplay());
        self::assertStringNotContainsString('trait', $this->tester->getDisplay());
    }

    public function testRunWithMissingType(): void
    {
        $this->tester->run([
            'path' => [__DIR__ . '/../_stubs/MissingReturnTypeClass.php'],
        ]);

        self::assertStringContainsString('missing return type', $this->tester->getDisplay());
    }
}
