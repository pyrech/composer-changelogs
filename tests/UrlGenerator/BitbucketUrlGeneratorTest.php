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

use Pyrech\ComposerChangelogs\UrlGenerator\BitbucketUrlGenerator;

class BitbucketUrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var BitbucketUrlGenerator */
    private $SUT;

    protected function setUp()
    {
        $this->SUT = new BitbucketUrlGenerator();
    }

    public function test_it_supports_bitbucket_urls()
    {
        $this->assertTrue($this->SUT->supports('https://bitbucket.org/mailchimp/mandrill-api-php.git'));
        $this->assertTrue($this->SUT->supports('https://bitbucket.org/rogoOOS/rog'));
    }

    public function test_it_does_not_support_non_bitbucket_urls()
    {
        $this->assertFalse($this->SUT->supports('https://github.com/phpunit/phpunit-mock-objects.git'));
        $this->assertFalse($this->SUT->supports('https://github.com/symfony/console'));
    }

    public function test_it_generate_compare_urls()
    {
        $this->assertSame(
            'https://bitbucket.org/acme/repo/branches/compare/v1.0.1%0Dv1.0.0',
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme/repo',
                'v1.0.0',
                'v1.0.1'
            )
        );

        $this->assertSame(
            'https://bitbucket.org/acme/repo/branches/compare/v1.0.2%0Dv1.0.1',
            $this->SUT->generateCompareUrl(
                'https://bitbucket.org/acme/repo.git',
                'v1.0.1',
                'v1.0.2'
            )
        );
    }

    public function test_it_does_not_generate_release_urls()
    {
        $this->assertFalse($this->SUT->generateReleaseUrl(
            'https://bitbucket.org/acme/repo',
            'v1.0.1'
        ));

        $this->assertFalse($this->SUT->generateReleaseUrl(
            'https://bitbucket.org/acme/repo.git',
            'v1.0.2'
        ));
    }
}
