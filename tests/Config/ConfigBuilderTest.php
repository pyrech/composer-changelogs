<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\Config;

use PHPUnit\Framework\TestCase;
use Pyrech\ComposerChangelogs\Config\ConfigBuilder;

class ConfigBuilderTest extends TestCase
{
    public const COMMIT_BIN_FILE = '../fixtures/bin/fake.sh';

    /** @var string */
    private $absoluteCommitBinFile;

    /** @var ConfigBuilder */
    private $SUT;

    protected function setUp(): void
    {
        $this->absoluteCommitBinFile = realpath(__DIR__ . '/' . self::COMMIT_BIN_FILE);
        $this->SUT = new ConfigBuilder();
    }

    public function testItHasADefaultSetup()
    {
        $extra = [];

        $config = $this->SUT->build($extra, __DIR__);

        $this->assertInstanceOf('Pyrech\ComposerChangelogs\Config\Config', $config);
        $this->assertSame('never', $config->getCommitAuto());
        $this->assertNull($config->getCommitBinFile());
        $this->assertEmpty($config->getGitlabHosts());
        $this->assertEquals(-1, $config->getPostUpdatePriority());

        $this->assertCount(0, $this->SUT->getWarnings());
    }

    public function testItWarnsWhenCommitAutoOptionIsInvalid()
    {
        $extra = [
            'commit-auto' => 'foo',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        $this->assertInstanceOf('Pyrech\ComposerChangelogs\Config\Config', $config);
        $this->assertSame('never', $config->getCommitAuto());
        $this->assertNull($config->getCommitBinFile());
        $this->assertEmpty($config->getGitlabHosts());
        $this->assertEquals(-1, $config->getPostUpdatePriority());

        $this->assertCount(1, $this->SUT->getWarnings());
        $this->assertStringContainsString('Invalid value "foo" for option "commit-auto"', $this->SUT->getWarnings()[0]);
    }

    public function testItWarnsWhenSpecifyingCommitBinFileAndNeverAutoCommit()
    {
        $extra = [
            'commit-auto' => 'never',
            'commit-bin-file' => self::COMMIT_BIN_FILE,
        ];

        $config = $this->SUT->build($extra, __DIR__);

        $this->assertInstanceOf('Pyrech\ComposerChangelogs\Config\Config', $config);
        $this->assertSame('never', $config->getCommitAuto());
        $this->assertNull($config->getCommitBinFile());
        $this->assertEmpty($config->getGitlabHosts());
        $this->assertEquals(-1, $config->getPostUpdatePriority());

        $this->assertCount(1, $this->SUT->getWarnings());
        $this->assertStringContainsString('"commit-bin-file" is specified but "commit-auto" option is set to "never". Ignoring.', $this->SUT->getWarnings()[0]);
    }

    public function testItWarnsWhenSpecifiedCommitBinFileWasNotFound()
    {
        $extra = [
            'commit-auto' => 'always',
            'commit-bin-file' => '/tmp/toto',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        $this->assertInstanceOf('Pyrech\ComposerChangelogs\Config\Config', $config);
        $this->assertSame('always', $config->getCommitAuto());
        $this->assertNull($config->getCommitBinFile());
        $this->assertEmpty($config->getGitlabHosts());
        $this->assertEquals(-1, $config->getPostUpdatePriority());

        $this->assertCount(1, $this->SUT->getWarnings());
        $this->assertStringContainsString('The file pointed by the option "commit-bin-file" was not found. Ignoring.', $this->SUT->getWarnings()[0]);
    }

    public function testItWarnsWhenCommitBinFileShouldHaveBeenSpecified()
    {
        $extra = [
            'commit-auto' => 'ask',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        $this->assertInstanceOf('Pyrech\ComposerChangelogs\Config\Config', $config);
        $this->assertSame('ask', $config->getCommitAuto());
        $this->assertNull($config->getCommitBinFile());
        $this->assertEmpty($config->getGitlabHosts());
        $this->assertEquals(-1, $config->getPostUpdatePriority());

        $this->assertCount(1, $this->SUT->getWarnings());
        $this->assertStringContainsString('"commit-auto" is set to "ask" but "commit-bin-file" was not specified.', $this->SUT->getWarnings()[0]);
    }

    public function testItWarnsWhenCommitEventPriorityValueIsInvalid()
    {
        $extra = [
            'post-update-priority' => 'invalid-priority',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        $this->assertInstanceOf('Pyrech\ComposerChangelogs\Config\Config', $config);
        $this->assertSame('never', $config->getCommitAuto());
        $this->assertNull($config->getCommitBinFile());
        $this->assertEmpty($config->getGitlabHosts());
        $this->assertEquals(-1, $config->getPostUpdatePriority());

        $this->assertCount(1, $this->SUT->getWarnings());
        $this->assertStringContainsString('"post-update-priority" is specified but not an integer. Ignoring and using default commit event priority.', $this->SUT->getWarnings()[0]);
    }

    public function testItWarnsWhenGitlabHostsIsNotAnArray()
    {
        $extra = [
            'gitlab-hosts' => 'gitlab.company1.com',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        $this->assertInstanceOf('Pyrech\ComposerChangelogs\Config\Config', $config);
        $this->assertSame('never', $config->getCommitAuto());
        $this->assertNull($config->getCommitBinFile());
        $this->assertEmpty($config->getGitlabHosts());
        $this->assertEquals(-1, $config->getPostUpdatePriority());

        $this->assertCount(1, $this->SUT->getWarnings());
        $this->assertStringContainsString('"gitlab-hosts" is specified but should be an array. Ignoring.', $this->SUT->getWarnings()[0]);
    }

    public function testItAcceptsValidSetup()
    {
        $extra = [
            'commit-auto' => 'ask',
            'commit-bin-file' => self::COMMIT_BIN_FILE,
            'gitlab-hosts' => ['gitlab.company1.com', 'gitlab.company2.com'],
            'post-update-priority' => '-1337',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        $this->assertInstanceOf('Pyrech\ComposerChangelogs\Config\Config', $config);
        $this->assertSame('ask', $config->getCommitAuto());
        $this->assertSame($this->absoluteCommitBinFile, $config->getCommitBinFile());
        $this->assertCount(2, $config->getGitlabHosts());
        $this->assertEquals(-1337, $config->getPostUpdatePriority());

        $this->assertCount(0, $this->SUT->getWarnings());
    }
}
