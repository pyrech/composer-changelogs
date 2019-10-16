<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\UrlGenerator;

use PHPUnit\Framework\TestCase;
use Pyrech\ComposerChangelogs\UrlGenerator\GithubUrlGenerator;
use Pyrech\ComposerChangelogs\Version;

class GithubUrlGeneratorTest extends TestCase
{
    /** @var GithubUrlGenerator */
    private $SUT;

    protected function setUp(): void
    {
        $this->SUT = new GithubUrlGenerator();
    }

    public function test_it_supports_github_urls()
    {
        $this->assertTrue($this->SUT->supports('https://github.com/phpunit/phpunit-mock-objects.git'));
        $this->assertTrue($this->SUT->supports('https://github.com/symfony/console'));
        $this->assertTrue($this->SUT->supports('git@github.com:private/repo.git'));
    }

    public function test_it_does_not_support_non_github_urls()
    {
        $this->assertFalse($this->SUT->supports('https://bitbucket.org/mailchimp/mandrill-api-php.git'));
        $this->assertFalse($this->SUT->supports('https://bitbucket.org/rogoOOS/rog'));
    }

    public function test_it_generates_compare_urls_with_or_without_git_extension_in_source_url()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://github.com/acme/repo/compare/v1.0.0...v1.0.1',
            $this->SUT->generateCompareUrl(
                'https://github.com/acme/repo',
                $versionFrom,
                'https://github.com/acme/repo',
                $versionTo
            )
        );

        $this->assertSame(
            'https://github.com/acme/repo/compare/v1.0.0...v1.0.1',
            $this->SUT->generateCompareUrl(
                'https://github.com/acme/repo.git',
                $versionFrom,
                'https://github.com/acme/repo.git',
                $versionTo
            )
        );
    }

    public function test_it_generates_compare_urls_with_dev_versions()
    {
        $versionFrom = new Version('v.1.0.9999999.9999999-dev', 'dev-master', 'dev-master 1234abc');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://github.com/acme/repo/compare/1234abc...v1.0.1',
            $this->SUT->generateCompareUrl(
                'https://github.com/acme/repo.git',
                $versionFrom,
                'https://github.com/acme/repo.git',
                $versionTo
            )
        );

        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('9999999-dev', 'dev-master', 'dev-master 6789def');

        $this->assertSame(
            'https://github.com/acme/repo/compare/v1.0.0...6789def',
            $this->SUT->generateCompareUrl(
                'https://github.com/acme/repo.git',
                $versionFrom,
                'https://github.com/acme/repo.git',
                $versionTo
            )
        );

        $versionFrom = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');
        $versionTo = new Version('dev-fix/issue', 'dev-fix/issue', 'dev-fix/issue 1234abc');

        $this->assertSame(
            'https://github.com/acme/repo/compare/v1.0.1...1234abc',
            $this->SUT->generateCompareUrl(
                'https://github.com/acme/repo.git',
                $versionFrom,
                'https://github.com/acme/repo.git',
                $versionTo
            )
        );
    }

    public function test_it_generates_compare_urls_across_forks()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://github.com/acme2/repo/compare/acme1:v1.0.0...acme2:v1.0.1',
            $this->SUT->generateCompareUrl(
                'https://github.com/acme1/repo',
                $versionFrom,
                'https://github.com/acme2/repo',
                $versionTo
            )
        );
    }

    public function test_it_does_not_generate_compare_urls_for_unsupported_url()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertFalse(
            $this->SUT->generateCompareUrl(
                '/home/toto/work/my-package',
                $versionFrom,
                'https://github.com/acme2/repo',
                $versionTo
            )
        );

        $this->assertFalse(
            $this->SUT->generateCompareUrl(
                'https://github.com/acme1/repo',
                $versionFrom,
                '/home/toto/work/my-package',
                $versionTo
            )
        );
    }

    public function test_it_throws_exception_when_generating_compare_urls_across_forks_if_a_source_url_is_invalid()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unrecognized url format for github.com ("https://github.com/acme2")');

        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->SUT->generateCompareUrl(
            'https://github.com/acme1/repo',
            $versionFrom,
            'https://github.com/acme2',
            $versionTo
        );
    }

    public function test_it_generates_compare_urls_with_ssh_source_url()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://github.com/acme/repo/compare/v1.0.0...v1.0.1',
            $this->SUT->generateCompareUrl(
                'git@github.com:acme/repo.git',
                $versionFrom,
                'git@github.com:acme/repo.git',
                $versionTo
            )
        );
    }

    public function test_it_does_not_generate_release_urls_for_dev_version()
    {
        $this->assertFalse(
            $this->SUT->generateReleaseUrl(
                'https://github.com/acme/repo',
                new Version('9999999-dev', 'dev-master', 'dev-master 1234abc')
            )
        );

        $this->assertFalse(
            $this->SUT->generateReleaseUrl(
                'https://github.com/acme/repo',
                new Version('dev-fix/issue', 'dev-fix/issue', 'dev-fix/issue 1234abc')
            )
        );
    }

    public function test_it_generates_release_urls()
    {
        $this->assertSame(
            'https://github.com/acme/repo/releases/tag/v1.0.1',
            $this->SUT->generateReleaseUrl(
                'https://github.com/acme/repo',
                new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
            )
        );

        $this->assertSame(
            'https://github.com/acme/repo/releases/tag/v1.0.1',
            $this->SUT->generateReleaseUrl(
                'https://github.com/acme/repo.git',
                new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
            )
        );
    }

    public function test_it_generates_release_url_with_ssh_source_url()
    {
        $this->assertSame(
            'https://github.com/acme/repo/releases/tag/v1.0.1',
            $this->SUT->generateReleaseUrl(
                'git@github.com:acme/repo.git',
                new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
            )
        );
    }
}
