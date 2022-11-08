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
use Pyrech\ComposerChangelogs\UrlGenerator\BitbucketUrlGenerator;
use Pyrech\ComposerChangelogs\Version;

class BitbucketUrlGeneratorTest extends TestCase
{
    /** @var BitbucketUrlGenerator */
    private $SUT;

    protected function setUp(): void
    {
        $this->SUT = new BitbucketUrlGenerator();
    }

    public function testItSupportsBitbucketUrls()
    {
        $this->assertTrue($this->SUT->supports('https://bitbucket.org/mailchimp/mandrill-api-php.git'));
        $this->assertTrue($this->SUT->supports('https://bitbucket.org/rogoOOS/rog'));
        $this->assertTrue($this->SUT->supports('git@bitbucket.org:private/repo.git'));
    }

    public function testItDoesNotSupportNonBitbucketUrls()
    {
        $this->assertFalse($this->SUT->supports('https://github.com/phpunit/phpunit-mock-objects.git'));
        $this->assertFalse($this->SUT->supports('https://github.com/symfony/console'));
        $this->assertFalse($this->SUT->supports(null));
    }

    public function testItGeneratesCompareUrlsWithOrWithoutGitExtensionInSourceUrl()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://bitbucket.org/acme/repo/branches/compare/v1.0.1%0Dv1.0.0',
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme/repo',
                $versionFrom,
                'https://bitbucket.org/acme/repo',
                $versionTo
            )
        );

        $this->assertSame(
            'https://bitbucket.org/acme/repo/branches/compare/v1.0.1%0Dv1.0.0',
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme/repo.git',
                $versionFrom,
                'https://bitbucket.org/acme/repo.git',
                $versionTo
            )
        );
    }

    public function testItGeneratesCompareUrlsWithDevVersions()
    {
        $versionFrom = new Version('v1.0.9999999.9999999-dev', 'dev-master', 'dev-master 1234abc');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://bitbucket.org/acme/repo/branches/compare/v1.0.1%0D1234abc',
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme/repo.git',
                $versionFrom,
                'https://bitbucket.org/acme/repo.git',
                $versionTo
            )
        );

        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('9999999-dev', 'dev-master', 'dev-master 6789def');

        $this->assertSame(
            'https://bitbucket.org/acme/repo/branches/compare/6789def%0Dv1.0.0',
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme/repo.git',
                $versionFrom,
                'https://bitbucket.org/acme/repo.git',
                $versionTo
            )
        );

        $versionFrom = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');
        $versionTo = new Version('dev-fix/issue', 'dev-fix/issue', 'dev-fix/issue 1234abc');

        $this->assertSame(
            'https://bitbucket.org/acme/repo/branches/compare/1234abc%0Dv1.0.1',
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme/repo.git',
                $versionFrom,
                'https://bitbucket.org/acme/repo.git',
                $versionTo
            )
        );
    }

    public function testItGeneratesCompareUrlsAcrossForks()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://bitbucket.org/acme2/repo/branches/compare/acme2/repo:v1.0.1%0Dacme1/repo:v1.0.0',
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme1/repo',
                $versionFrom,
                'https://bitbucket.org/acme2/repo',
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
                'https://bitbucket.org/acme2/repo',
                $versionTo
            )
        );

        $this->assertFalse(
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme1/repo',
                $versionFrom,
                '/home/toto/work/my-package',
                $versionTo
            )
        );
    }

    public function testItThrowsExceptionWhenGeneratingCompareUrlsAcrossForksIfASourceUrlIsInvalid()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unrecognized url format for bitbucket.org ("https://bitbucket.org/acme2")');

        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->SUT->generateCompareUrl(
            'https://bitbucket.org/acme1/repo',
            $versionFrom,
            'https://bitbucket.org/acme2',
            $versionTo
        );
    }

    public function testItGeneratesCompareUrlsWithSshSourceUrl()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://bitbucket.org/acme/repo/branches/compare/v1.0.1%0Dv1.0.0',
            $this->SUT->generateCompareUrl(
                'git@bitbucket.org:acme/repo.git',
                $versionFrom,
                'git@bitbucket.org:acme/repo.git',
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

    public function testItDoesNotGenerateReleaseUrls()
    {
        $this->assertFalse(
            $this->SUT->generateReleaseUrl(
                'https://bitbucket.org/acme/repo',
                new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
            )
        );

        $this->assertFalse(
            $this->SUT->generateReleaseUrl(
                'https://bitbucket.org/acme/repo.git',
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
