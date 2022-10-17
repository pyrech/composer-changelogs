<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests;

use Composer\Composer;
use Composer\Config;
use Composer\DependencyResolver\DefaultPolicy;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Request;
use Composer\Factory;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\BufferIO;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Composer\Plugin\PluginInterface;
use Composer\Repository\CompositeRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Script\ScriptEvents;
use PHPUnit\Framework\TestCase;
use Pyrech\ComposerChangelogs\ChangelogsPlugin;

class ChangelogsPluginTest extends TestCase
{
    /** @var BufferIO */
    private $io;

    /** @var Composer */
    private $composer;

    /** @var Config */
    private $config;

    /** @var string */
    private $tempDir;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->tempDir = __DIR__ . '/temp';
        $this->config = new Config(false, realpath(__DIR__ . '/fixtures/local'));
        $this->config->merge([
            'config' => [
                'home' => __DIR__,
            ],
        ]);

        $this->io = new BufferIO();

        $this->composer = Factory::create($this->io, null, false);
        $this->composer->setConfig($this->config);
        $this->composer->setPackage(new RootPackage('my/project', '1.0.0', '1.0.0'));

        self::cleanTempDir();
        mkdir($this->tempDir);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        self::cleanTempDir();
    }

    /**
     * Completely remove the temp dir and its content if it exists.
     */
    private function cleanTempDir()
    {
        if (!is_dir($this->tempDir)) {
            return;
        }
        $files = glob($this->tempDir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($this->tempDir);
    }

    public function testItIsRegisteredAndActivated()
    {
        $plugin = new ChangelogsPlugin();

        $this->addComposerPlugin($plugin);

        $this->assertSame([$plugin], $this->composer->getPluginManager()->getPlugins());
    }

    public function testItReceivesEvent()
    {
        $this->addComposerPlugin(new ChangelogsPlugin());

        $operation = $this->getUpdateOperation();

        $this->dispatchPostPackageUpdateEvent($operation);

        $this->composer->getEventDispatcher()->dispatchScript(ScriptEvents::POST_UPDATE_CMD);

        $expectedOutput = <<<OUTPUT
Changelogs summary:

 - foo/bar updated from v1.0.0 to v1.0.1 patch
   See changes: https://github.com/foo/bar/compare/v1.0.0...v1.0.1
   Release notes: https://github.com/foo/bar/releases/tag/v1.0.1


OUTPUT;

        $this->assertSame($expectedOutput, $this->io->getOutput());
    }

    public function testEventsAreHandled()
    {
        $plugin = new ChangelogsPlugin();
        $plugin->activate($this->composer, $this->io);

        $operation = $this->getUpdateOperation();

        $packageEvent = $this->createPostPackageUpdateEvent($operation);

        $plugin->postPackageOperation($packageEvent);

        $plugin->postUpdate();

        $expectedOutput = <<<OUTPUT
Changelogs summary:

 - foo/bar updated from v1.0.0 to v1.0.1 patch
   See changes: https://github.com/foo/bar/compare/v1.0.0...v1.0.1
   Release notes: https://github.com/foo/bar/releases/tag/v1.0.1


OUTPUT;

        $this->assertSame($expectedOutput, $this->io->getOutput());
    }

    public function testPostUpdateEventPriorityIsHandled()
    {
        $this->config->merge([
            'config' => [
                'home' => realpath(__DIR__ . '/fixtures/other-post-update-priority'),
            ],
        ]);

        $this->addComposerPlugin(new ChangelogsPlugin());

        $eventDispatcherReflection = new \ReflectionClass($this->composer->getEventDispatcher());
        $eventListenerReflection = $eventDispatcherReflection->getProperty('listeners');
        $eventListenerReflection->setAccessible(true);
        $eventListeners = $eventListenerReflection->getValue($this->composer->getEventDispatcher());

        $this->assertArrayHasKey(ScriptEvents::POST_UPDATE_CMD, $eventListeners);
        $this->assertArrayHasKey(-1337, $eventListeners[ScriptEvents::POST_UPDATE_CMD]);
    }

    public function testItCommitsWithAlwaysOption()
    {
        $this->config->merge([
            'config' => [
                'home' => realpath(__DIR__ . '/fixtures/home'),
            ],
        ]);

        $plugin = new ChangelogsPlugin();
        $plugin->activate($this->composer, $this->io);

        $operation = $this->getUpdateOperation();

        $packageEvent = $this->createPostPackageUpdateEvent($operation);

        $plugin->postPackageOperation($packageEvent);

        $plugin->postUpdate();

        $this->assertStringMatchesFormat('%aExecuting following command: %s/tests/fixtures/bin/fake.sh \'%s\' \'%s/composer-changelogs-%s\'', $this->io->getOutput());
    }

    public function testItCommitsWithDefaultCommitMessage()
    {
        $this->config->merge([
            'config' => [
                'home' => realpath(__DIR__ . '/fixtures/home'),
            ],
        ]);

        $plugin = new ChangelogsPlugin();
        $plugin->activate($this->composer, $this->io);

        $operation = $this->getUpdateOperation();

        $packageEvent = $this->createPostPackageUpdateEvent($operation);

        $plugin->postPackageOperation($packageEvent);

        $plugin->postUpdate();

        $this->assertFileExists($this->tempDir . '/commit-message.txt');
        $commitMessage = file_get_contents($this->tempDir . '/commit-message.txt');
        $this->assertStringMatchesFormat('Update dependencies%aChangelogs summary:%a', $commitMessage);
    }

    public function testItCommitsWithCustomCommitMessage()
    {
        $this->config->merge([
            'config' => [
                'home' => realpath(__DIR__ . '/fixtures/home-commit-message'),
            ],
        ]);

        $plugin = new ChangelogsPlugin();
        $plugin->activate($this->composer, $this->io);

        $operation = $this->getUpdateOperation();

        $packageEvent = $this->createPostPackageUpdateEvent($operation);

        $plugin->postPackageOperation($packageEvent);

        $plugin->postUpdate();

        $this->assertFileExists($this->tempDir . '/commit-message.txt');
        $commitMessage = file_get_contents($this->tempDir . '/commit-message.txt');
        $this->assertStringMatchesFormat('chore: Update composer%aChangelogs summary:%a', $commitMessage);
    }

    private function addComposerPlugin(PluginInterface $plugin)
    {
        $sourcePackage = new Package('pyrech/composer-changelogs', '1', 'v1');

        $pluginManager = $this->composer->getPluginManager();
        $pluginManager->addPlugin($plugin, false, $sourcePackage);
    }

    /**
     * @return UpdateOperation
     */
    private function getUpdateOperation()
    {
        $initialPackage = new Package('foo/bar', '1.0.0.0', 'v1.0.0');
        $initialPackage->setSourceUrl('https://github.com/foo/bar.git');

        $targetPackage = new Package('foo/bar', '1.0.1.0', 'v1.0.1');
        $targetPackage->setSourceUrl('https://github.com/foo/bar.git');

        return new UpdateOperation($initialPackage, $targetPackage);
    }

    private function createPostPackageUpdateEvent($operation)
    {
        if (version_compare(PluginInterface::PLUGIN_API_VERSION, '2.0.0') >= 0) {
            return new PackageEvent(
                PackageEvents::POST_PACKAGE_UPDATE,
                $this->composer,
                $this->io,
                false,
                $this->createMock(RepositoryInterface::class),
                [$operation],
                $operation
            );
        }

        return new PackageEvent(
            PackageEvents::POST_PACKAGE_UPDATE,
            $this->composer,
            $this->io,
            false,
            new DefaultPolicy(false, false),
            new Pool(),
            new CompositeRepository([]),
            new Request(new Pool()),
            [$operation],
            $operation
        );
    }

    private function dispatchPostPackageUpdateEvent($operation)
    {
        if (version_compare(PluginInterface::PLUGIN_API_VERSION, '2.0.0') >= 0) {
            $this->composer->getEventDispatcher()->dispatchPackageEvent(
                PackageEvents::POST_PACKAGE_UPDATE,
                false,
                $this->createMock(RepositoryInterface::class),
                [$operation],
                $operation
            );

            return;
        }

        $this->composer->getEventDispatcher()->dispatchPackageEvent(
            PackageEvents::POST_PACKAGE_UPDATE,
            false,
            new DefaultPolicy(false, false),
            new Pool(),
            new CompositeRepository([]),
            new Request(new Pool()),
            [$operation],
            $operation
        );
    }
}
