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
use Pyrech\ComposerChangelogs\Config\Config;
use Pyrech\ComposerChangelogs\Config\ConfigBuilder;

class ConfigBuilderTest extends TestCase
{
    const COMMIT_BIN_FILE = '../fixtures/bin/fake.sh';

    /** @var string */
    private $absoluteCommitBinFile;

    /** @var ConfigBuilder */
    private $SUT;

    public function setUp(): void
    {
        $this->absoluteCommitBinFile = realpath(__DIR__ . '/' . self::COMMIT_BIN_FILE);
        $this->SUT = new ConfigBuilder();
    }

    public function test_it_has_a_default_setup()
    {
        $extra = [];

        $config = $this->SUT->build($extra, __DIR__);

        static::assertInstanceOf(Config::class, $config);
        static::assertSame('never', $config->getCommitAuto());
        static::assertNull($config->getCommitBinFile());
        static::assertEmpty($config->getGitlabHosts());
        static::assertEquals(-1, $config->getPostUpdatePriority());

        static::assertCount(0, $this->SUT->getWarnings());
    }

    public function test_it_warns_when_commit_auto_option_is_invalid()
    {
        $extra = [
            'commit-auto' => 'foo',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        static::assertInstanceOf(Config::class, $config);
        static::assertSame('never', $config->getCommitAuto());
        static::assertNull($config->getCommitBinFile());
        static::assertEmpty($config->getGitlabHosts());
        static::assertEquals(-1, $config->getPostUpdatePriority());

        static::assertCount(1, $this->SUT->getWarnings());

        static::assertStringContainsString('Invalid value "foo" for option "commit-auto"', $this->SUT->getWarnings()[0]);
    }

    public function test_it_warns_when_specifying_commit_bin_file_and_never_auto_commit()
    {
        $extra = [
            'commit-auto' => 'never',
            'commit-bin-file' => self::COMMIT_BIN_FILE,
        ];

        $config = $this->SUT->build($extra, __DIR__);

        static::assertInstanceOf(Config::class, $config);
        static::assertSame('never', $config->getCommitAuto());
        static::assertNull($config->getCommitBinFile());
        static::assertEmpty($config->getGitlabHosts());
        static::assertEquals(-1, $config->getPostUpdatePriority());

        static::assertCount(1, $this->SUT->getWarnings());
        static::assertStringContainsString('"commit-bin-file" is specified but "commit-auto" option is set to "never". Ignoring.', $this->SUT->getWarnings()[0]);
    }

    public function test_it_warns_when_specified_commit_bin_file_was_not_found()
    {
        $extra = [
            'commit-auto' => 'always',
            'commit-bin-file' => '/tmp/toto',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        static::assertInstanceOf(Config::class, $config);
        static::assertSame('always', $config->getCommitAuto());
        static::assertNull($config->getCommitBinFile());
        static::assertEmpty($config->getGitlabHosts());
        static::assertEquals(-1, $config->getPostUpdatePriority());

        static::assertCount(1, $this->SUT->getWarnings());
        static::assertStringContainsString('The file pointed by the option "commit-bin-file" was not found. Ignoring.', $this->SUT->getWarnings()[0]);
    }

    public function test_it_warns_when_commit_bin_file_should_have_been_specified()
    {
        $extra = [
            'commit-auto' => 'ask',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        static::assertInstanceOf(Config::class, $config);
        static::assertSame('ask', $config->getCommitAuto());
        static::assertNull($config->getCommitBinFile());
        static::assertEmpty($config->getGitlabHosts());
        static::assertEquals(-1, $config->getPostUpdatePriority());

        static::assertCount(1, $this->SUT->getWarnings());
        static::assertStringContainsString('"commit-auto" is set to "ask" but "commit-bin-file" was not specified.', $this->SUT->getWarnings()[0]);
    }

    public function test_it_warns_when_commit_event_priority_value_is_invalid()
    {
        $extra = [
            'post-update-priority' => 'invalid-priority',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        static::assertInstanceOf(Config::class, $config);
        static::assertSame('never', $config->getCommitAuto());
        static::assertNull($config->getCommitBinFile());
        static::assertEmpty($config->getGitlabHosts());
        static::assertEquals(-1, $config->getPostUpdatePriority());

        static::assertCount(1, $this->SUT->getWarnings());
        static::assertStringContainsString('"post-update-priority" is specified but not an integer. Ignoring and using default commit event priority.', $this->SUT->getWarnings()[0]);
    }

    public function test_it_warns_when_gitlab_hosts_is_not_an_array()
    {
        $extra = [
            'gitlab-hosts' => 'gitlab.company1.com',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        static::assertInstanceOf(Config::class, $config);
        static::assertSame('never', $config->getCommitAuto());
        static::assertNull($config->getCommitBinFile());
        static::assertEmpty($config->getGitlabHosts());
        static::assertEquals(-1, $config->getPostUpdatePriority());

        static::assertCount(1, $this->SUT->getWarnings());
        static::assertStringContainsString('"gitlab-hosts" is specified but should be an array. Ignoring.', $this->SUT->getWarnings()[0]);
    }

    public function test_it_accepts_valid_setup()
    {
        $extra = [
            'commit-auto' => 'ask',
            'commit-bin-file' => self::COMMIT_BIN_FILE,
            'gitlab-hosts' => ['gitlab.company1.com', 'gitlab.company2.com'],
            'post-update-priority' => '-1337',
        ];

        $config = $this->SUT->build($extra, __DIR__);

        static::assertInstanceOf(Config::class, $config);
        static::assertSame('ask', $config->getCommitAuto());
        static::assertSame($this->absoluteCommitBinFile, $config->getCommitBinFile());
        static::assertCount(2, $config->getGitlabHosts());
        static::assertEquals(-1337, $config->getPostUpdatePriority());

        static::assertCount(0, $this->SUT->getWarnings());
    }
}
