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
use Pyrech\ComposerChangelogs\UrlGenerator\WordPressUrlGenerator;
use Pyrech\ComposerChangelogs\Version;

class WordPressUrlGeneratorTest extends TestCase
{
    /**
     * @var WordPressUrlGenerator
     */
    private $SUT;

    protected function setUp(): void
    {
        $this->SUT = new WordPressUrlGenerator();
    }

    public function testItSupportsWordpressUrls()
    {
        $this->assertTrue($this->SUT->supports('http://plugins.svn.wordpress.org/social-networks-auto-poster-facebook-twitter-g/'));
        $this->assertTrue($this->SUT->supports('http://plugins.svn.wordpress.org/askimet/'));
        $this->assertTrue($this->SUT->supports('http://themes.svn.wordpress.org/minimize/'));
    }

    public function testItDoesNotSupportNonWordpressUrls()
    {
        $this->assertFalse($this->SUT->supports('https://github.com/phpunit/phpunit-mock-objects.git'));
        $this->assertFalse($this->SUT->supports('https://github.com/symfony/console'));
        $this->assertFalse($this->SUT->supports('https://bitbucket.org/mailchimp/mandrill-api-php.git'));
        $this->assertFalse($this->SUT->supports('https://bitbucket.org/rogoOOS/rog'));
    }

    public function testItGeneratesCompareUrls()
    {
        $versionFrom = new Version('v1.0.0.0', 'v1.0.0', 'v1.0.0');
        $versionTo = new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1');

        $this->assertSame(
            'https://wordpress.org/plugins/askimet/changelog/',
            $this->SUT->generateCompareUrl(
                'http://plugins.svn.wordpress.org/askimet/',
                $versionFrom,
                'http://plugins.svn.wordpress.org/askimet/',
                $versionTo
            )
        );

        $this->assertSame(
            'https://themes.trac.wordpress.org/log/minimize/',
            $this->SUT->generateCompareUrl(
                'http://themes.svn.wordpress.org/minimize/',
                $versionFrom,
                'http://themes.svn.wordpress.org/minimize/',
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
        $this->assertFalse($this->SUT->generateReleaseUrl(
            'http://themes.svn.wordpress.org/minimize/',
            new Version('v1.0.1.0', 'v1.0.1', 'v1.0.1')
        ));
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
