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

use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\Package\Package;
use PHPUnit\Framework\TestCase;
use Pyrech\ComposerChangelogs\OperationHandler\InstallHandler;
use Pyrech\ComposerChangelogs\tests\resources\FakeOperation;
use Pyrech\ComposerChangelogs\tests\resources\FakeUrlGenerator;

class InstallHandlerTest extends TestCase
{
    /** @var InstallHandler */
    private $SUT;

    protected function setUp(): void
    {
        $this->SUT = new InstallHandler();
    }

    public function testItSupportsInstallOperation()
    {
        $operation = new InstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $this->assertTrue($this->SUT->supports($operation));
    }

    public function testItDoesNotSupportNonInstallOperation()
    {
        $this->assertFalse($this->SUT->supports(new FakeOperation('')));
    }

    public function testItExtractsSourceUrl()
    {
        $package = new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0');
        $package->setSourceUrl('https://example.com/acme/my-project.git');

        $operation = new InstallOperation($package);

        $this->assertSame(
            'https://example.com/acme/my-project.git',
            $this->SUT->extractSourceUrl($operation)
        );
    }

    public function testItThrowsExceptionWhenExtractingSourceUrlFromNonInstallOperation()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Operation should be an instance of InstallOperation');

        $this->SUT->extractSourceUrl(new FakeOperation(''));
    }

    public function testItGetsOutputWithoutUrlGenerator()
    {
        $package = new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0');
        $package->setSourceUrl('https://example.com/acme/my-project.git');

        $operation = new InstallOperation($package);

        $expectedOutput = [
            ' - <fg=green>acme/my-project</fg=green> installed in version <fg=yellow>v1.0.0</fg=yellow>',
        ];

        $this->assertSame(
            $expectedOutput,
            $this->SUT->getOutput($operation, null)
        );
    }

    public function testItGetsOutputWithUrlGeneratorNoSupportingCompareUrl()
    {
        $operation = new InstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $urlGenerator = new FakeUrlGenerator(
            true,
            false,
            'https://example.com/acme/my-project/release/v1.0.1'
        );

        $expectedOutput = [
            ' - <fg=green>acme/my-project</fg=green> installed in version <fg=yellow>v1.0.0</fg=yellow>',
            '   Release notes: https://example.com/acme/my-project/release/v1.0.1',
        ];

        $this->assertSame(
            $expectedOutput,
            $this->SUT->getOutput($operation, $urlGenerator)
        );
    }

    public function testItGetsOutputWithUrlGeneratorNoSupportingReleaseUrl()
    {
        $operation = new InstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $urlGenerator = new FakeUrlGenerator(
            true,
            'https://example.com/acme/my-project/compare/v1.0.0/v1.0.1',
            false
        );

        $expectedOutput = [
            ' - <fg=green>acme/my-project</fg=green> installed in version <fg=yellow>v1.0.0</fg=yellow>',
        ];

        $this->assertSame(
            $expectedOutput,
            $this->SUT->getOutput($operation, $urlGenerator)
        );
    }

    public function testItGetsOutputWithUrlGeneratorSupportingAllUrls()
    {
        $operation = new InstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $urlGenerator = new FakeUrlGenerator(
            true,
            'https://example.com/acme/my-project/compare/v1.0.0/v1.0.1',
            'https://example.com/acme/my-project/release/v1.0.1'
        );

        $expectedOutput = [
            ' - <fg=green>acme/my-project</fg=green> installed in version <fg=yellow>v1.0.0</fg=yellow>',
            '   Release notes: https://example.com/acme/my-project/release/v1.0.1',
        ];

        $this->assertSame(
            $expectedOutput,
            $this->SUT->getOutput($operation, $urlGenerator)
        );
    }

    public function testItThrowsExceptionWhenGettingOutputFromNonInstallOperation()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Operation should be an instance of InstallOperation');

        $this->SUT->getOutput(new FakeOperation(''));
    }
}
