<?php

/**
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
use Pyrech\ComposerChangelogs\OperationHandler\UninstallHandler;
use Pyrech\ComposerChangelogs\tests\resources\FakeOperation;
use Pyrech\ComposerChangelogs\tests\resources\FakeUrlGenerator;

class UninstallHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var UninstallHandler */
    private $SUT;

    protected function setUp()
    {
        $this->SUT = new UninstallHandler();
    }

    public function test_it_supports_uninstall_operation()
    {
        $operation = new UninstallOperation(
            new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0')
        );

        $this->assertTrue($this->SUT->supports($operation));
    }

    public function test_it_does_not_support_non_uninstall_operation()
    {
        $this->assertFalse($this->SUT->supports(new FakeOperation('')));
    }

    public function test_it_extracts_source_url()
    {
        $package = new Package('acme/my-project', 'v1.0.0.0', 'v1.0.0');
        $package->setSourceUrl('https://example.com/acme/my-project.git');

        $operation = new UninstallOperation($package);

        $this->assertSame(
            'https://example.com/acme/my-project.git',
            $this->SUT->extractSourceUrl($operation)
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Operation should be an instance of UninstallOperation
     */
    public function test_it_throws_exception_when_extracting_source_url_from_non_uninstall_operation()
    {
        $this->SUT->extractSourceUrl(new FakeOperation(''));
    }

    public function test_it_gets_output_without_url_generator()
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

    public function test_it_gets_output_with_url_generator_no_supporting_compare_url()
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

    public function test_it_gets_output_with_url_generator_no_supporting_release_url()
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

    public function test_it_gets_output_with_url_generator_supporting_all_urls()
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

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Operation should be an instance of UninstallOperation
     */
    public function test_it_throws_exception_when_getting_output_from_non_uninstall_operation()
    {
        $this->SUT->getOutput(new FakeOperation(''));
    }
}
