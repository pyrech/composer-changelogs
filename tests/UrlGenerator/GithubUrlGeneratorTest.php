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

use Pyrech\ComposerChangelogs\Update;
use Pyrech\ComposerChangelogs\UrlGenerator\GithubUrlGenerator;

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
        $this->assertFalse($this->SUT->supports('https://bitbucket.org/rogoOOS/rog/wiki/Home'));
    }

    public function test_it_generate_compare_urls()
    {
        $update = new Update('acme/project', 'v1.0.0', 'v1.0.1', 'https://github.com/acme/repo');
        $this->assertSame(
            'https://github.com/acme/repo/compare/v1.0.0...v1.0.1',
            $this->SUT->generateCompareUrl($update)
        );

        $update = new Update('acme/project', 'v1.0.1', 'v1.0.2', 'https://github.com/acme/repo.git');
        $this->assertSame(
            'https://github.com/acme/repo/compare/v1.0.1...v1.0.2',
            $this->SUT->generateCompareUrl($update)
        );
    }

    public function test_it_generate_release_urls()
    {
        $update = new Update('acme/project', 'v1.0.0', 'v1.0.1', 'https://github.com/acme/repo');
        $this->assertSame(
            'https://github.com/acme/repo/releases/tag/v1.0.1',
            $this->SUT->generateReleaseUrl($update)
        );

        $update = new Update('acme/project', 'v1.0.1', 'v1.0.2', 'https://github.com/acme/repo.git');
        $this->assertSame(
            'https://github.com/acme/repo/releases/tag/v1.0.2',
            $this->SUT->generateReleaseUrl($update)
        );
    }
}
