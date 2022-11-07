<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\OperationHandler;

use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\Package\Package;
use PHPUnit\Framework\TestCase;
use Pyrech\ComposerChangelogs\OperationHandler\UninstallHandler;
use Pyrech\ComposerChangelogs\tests\resources\FakeOperation;
use Pyrech\ComposerChangelogs\tests\resources\FakeUrlGenerator;

class UninstallHandlerTest extends TestCase
{
    /** @var UninstallHandler */
    private $SUT;

    protected function setUp(): void
    {
        $this->SUT = new UninstallHandler();
    }

    public function testItSupportsUninstallOperation()
    {
        $operation = new UninstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $this->assertTrue($this->SUT->supports($operation));
    }

    public function testItDoesNotSupportNonUninstallOperation()
    {
        $this->assertFalse($this->SUT->supports(new FakeOperation('')));
    }

    public function testItExtractsSourceUrl()
    {
        $package = new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0');
        $package->setSourceUrl('https://example.com/acme/my-project.git');

        $operation = new UninstallOperation($package);

        $this->assertSame(
            'https://example.com/acme/my-project.git',
            $this->SUT->extractSourceUrl($operation)
        );
    }

    public function testItThrowsExceptionWhenExtractingSourceUrlFromNonUninstallOperation()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Operation should be an instance of UninstallOperation');

        $this->SUT->extractSourceUrl(new FakeOperation(''));
    }

    public function testItGetsOutputWithoutUrlGenerator()
    {
        $package = new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0');
        $package->setSourceUrl('https://example.com/acme/my-project.git');

        $operation = new UninstallOperation($package);

        $expectedOutput = [
            ' - <fg=green>acme/my-project</fg=green> removed (installed version was <fg=yellow>v1.0.0</fg=yellow>)',
        ];

        $this->assertSame(
            $expectedOutput,
            $this->SUT->getOutput($operation, null)
        );
    }

    public function testItGetsOutputWithUrlGeneratorNoSupportingCompareUrl()
    {
        $operation = new UninstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $urlGenerator = new FakeUrlGenerator(
            true,
            false,
            'https://example.com/acme/my-project/release/v1.0.1'
        );

        $expectedOutput = [
            ' - <fg=green>acme/my-project</fg=green> removed (installed version was <fg=yellow>v1.0.0</fg=yellow>)',
        ];

        $this->assertSame(
            $expectedOutput,
            $this->SUT->getOutput($operation, $urlGenerator)
        );
    }

    public function testItGetsOutputWithUrlGeneratorNoSupportingReleaseUrl()
    {
        $operation = new UninstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $urlGenerator = new FakeUrlGenerator(
            true,
            'https://example.com/acme/my-project/compare/v1.0.0/v1.0.1',
            false
        );

        $expectedOutput = [
            ' - <fg=green>acme/my-project</fg=green> removed (installed version was <fg=yellow>v1.0.0</fg=yellow>)',
        ];

        $this->assertSame(
            $expectedOutput,
            $this->SUT->getOutput($operation, $urlGenerator)
        );
    }

    public function testItGetsOutputWithUrlGeneratorSupportingAllUrls()
    {
        $operation = new UninstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $urlGenerator = new FakeUrlGenerator(
            true,
            'https://example.com/acme/my-project/compare/v1.0.0/v1.0.1',
            'https://example.com/acme/my-project/release/v1.0.1'
        );

        $expectedOutput = [
            ' - <fg=green>acme/my-project</fg=green> removed (installed version was <fg=yellow>v1.0.0</fg=yellow>)',
        ];

        $this->assertSame(
            $expectedOutput,
            $this->SUT->getOutput($operation, $urlGenerator)
        );
    }

    public function testItThrowsExceptionWhenGettingOutputFromNonUninstallOperation()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Operation should be an instance of UninstallOperation');

        $this->SUT->getOutput(new FakeOperation(''));
    }
}
