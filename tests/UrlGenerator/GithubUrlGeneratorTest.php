<?php

/**
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\UrlGenerator;

use Pyrech\ComposerChangelogs\UrlGenerator\GithubUrlGenerator;
use Pyrech\ComposerChangelogs\Version;

class GithubUrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var GithubUrlGenerator */
    private $SUT;

    protected function setUp()
    {
        $this->SUT = new GithubUrlGenerator();
    }

    public function test_it_supports_github_urls()
    {
        $this->assertTrue($this->SUT->supports('https://github.com/phpunit/phpunit-mock-objects.git'));
        $this->assertTrue($this->SUT->supports('https://github.com/symfony/console'));
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
                $versionTo
            )
        );

        $this->assertSame(
            'https://github.com/acme/repo/compare/v1.0.0...v1.0.1',
            $this->SUT->generateCompareUrl(
                'https://github.com/acme/repo.git',
                $versionFrom,
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
}
