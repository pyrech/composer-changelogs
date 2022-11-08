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
use Pyrech\ComposerChangelogs\UrlGenerator\GitlabUrlGenerator;
use Pyrech\ComposerChangelogs\Version;

class GitlabUrlGeneratorTest extends TestCase
{
    /** @var GitlabUrlGenerator */
    private $SUT;

    protected function setUp(): void
    {
        $this->SUT = new GitlabUrlGenerator('gitlab.company.org');
    }

    public function testItSupportsGitlabUrls()
    {
        $this->assertTrue($this->SUT->supports('https://gitlab.company.org/phpunit/phpunit-mock-objects.git'));
        $this->assertTrue($this->SUT->supports('https://gitlab.company.org/symfony/console'));
        $this->assertTrue($this->SUT->supports('git@gitlab.company.org:private/repo.git'));
    }

    public function testItDoesNotSupportNonGitlabUrls()
    {
        $this->assertFalse($this->SUT->supports('https://company.org/about-us'));
        $this->assertFalse($this->SUT->supports('https://bitbucket.org/rogoOOS/rog'));
    }

    public function testItGeneratesCompareUrlsWithOrWithoutGitExtensionInSourceUrl()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://gitlab.company.org/acme/repo/compare/v1.0.0...v1.0.1',
            $this->SUT->generateCompareUrl(
                'https://gitlab.company.org/acme/repo',
                $versionFrom,
                'https://gitlab.company.org/acme/repo',
                $versionTo
            )
        );

        $this->assertSame(
            'https://gitlab.company.org/acme/repo/compare/v1.0.0...v1.0.1',
            $this->SUT->generateCompareUrl(
                'https://gitlab.company.org/acme/repo.git',
                $versionFrom,
                'https://gitlab.company.org/acme/repo.git',
                $versionTo
            )
        );
    }

    public function testItGeneratesCompareUrlsWithDevVersions()
    {
        $versionFrom = new Version('v.1.0.9999999.9999999-dev', 'dev-master', 'dev-master 1234abc');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://gitlab.company.org/acme/repo/compare/1234abc...v1.0.1',
            $this->SUT->generateCompareUrl(
                'https://gitlab.company.org/acme/repo.git',
                $versionFrom,
                'https://gitlab.company.org/acme/repo.git',
                $versionTo
            )
        );

        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('9999999-dev', 'dev-master', 'dev-master 6789def');

        $this->assertSame(
            'https://gitlab.company.org/acme/repo/compare/v1.0.0...6789def',
            $this->SUT->generateCompareUrl(
                'https://gitlab.company.org/acme/repo.git',
                $versionFrom,
                'https://gitlab.company.org/acme/repo.git',
                $versionTo
            )
        );

        $versionFrom = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');
        $versionTo = new Version('dev-fix/issue', 'dev-fix/issue', 'dev-fix/issue 1234abc');

        $this->assertSame(
            'https://gitlab.company.org/acme/repo/compare/v1.0.1...1234abc',
            $this->SUT->generateCompareUrl(
                'https://gitlab.company.org/acme/repo.git',
                $versionFrom,
                'https://gitlab.company.org/acme/repo.git',
                $versionTo
            )
        );
    }

    public function testItDoesNotGenerateCompareUrlsAcrossForks()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertFalse(
            $this->SUT->generateCompareUrl(
                'https://gitlab.company.org/acme1/repo',
                $versionFrom,
                'https://gitlab.company.org/acme2/repo',
                $versionTo
            )
        );
    }

    public function testItDoesNotGenerateCompareUrlsForUnsupportedUrl()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertFalse(
            $this->SUT->generateCompareUrl(
                '/home/toto/work/my-package',
                $versionFrom,
                'https://gitlab.company.org/acme2/repo',
                $versionTo
            )
        );

        $this->assertFalse(
            $this->SUT->generateCompareUrl(
                'https://gitlab.company.org/acme1/repo',
                $versionFrom,
                '/home/toto/work/my-package',
                $versionTo
            )
        );
    }

    public function testItGeneratesCompareUrlsWithSshSourceUrl()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://gitlab.company.org/acme/repo/compare/v1.0.0...v1.0.1',
            $this->SUT->generateCompareUrl(
                'git@gitlab.company.org:acme/repo.git',
                $versionFrom,
                'git@gitlab.company.org:acme/repo.git',
                $versionTo
            )
        );
    }

    public function testItDoesNotGenerateCompareUrlsWithoutSourceUrl()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertFalse(
            $this->SUT->generateCompareUrl(
                null,
                $versionFrom,
                null,
                $versionTo
            )
        );
    }

    public function testItDoesNotGenerateReleaseUrlsForDevVersion()
    {
        $this->assertFalse(
            $this->SUT->generateReleaseUrl(
                'https://gitlab.company.org/acme/repo',
                new Version('9999999-dev', 'dev-master', 'dev-master 1234abc')
            )
        );

        $this->assertFalse(
            $this->SUT->generateReleaseUrl(
                'https://gitlab.company.org/acme/repo',
                new Version('dev-fix/issue', 'dev-fix/issue', 'dev-fix/issue 1234abc')
            )
        );
    }

    public function testItGeneratesReleaseUrls()
    {
        $this->assertSame(
            'https://gitlab.company.org/acme/repo/tags/v1.0.1',
            $this->SUT->generateReleaseUrl(
                'https://gitlab.company.org/acme/repo',
                new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
            )
        );

        $this->assertSame(
            'https://gitlab.company.org/acme/repo/tags/v1.0.1',
            $this->SUT->generateReleaseUrl(
                'https://gitlab.company.org/acme/repo.git',
                new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
            )
        );
    }

    public function testItGeneratesReleaseUrlWithSshSourceUrl()
    {
        $this->assertSame(
            'https://gitlab.company.org/acme/repo/tags/v1.0.1',
            $this->SUT->generateReleaseUrl(
                'git@gitlab.company.org:acme/repo.git',
                new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
            )
        );
    }

    public function testItDoesNotGenerateReleaseUrlWithoutSourceUrl()
    {
        $this->assertFalse(
            $this->SUT->generateReleaseUrl(
                null,
                new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
            )
        );
    }
}
