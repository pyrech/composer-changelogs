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
use Composer\EventDispatcher\EventDispatcher;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\BufferIO;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginManager;
use Composer\Repository\CompositeRepository;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Pyrech\ComposerChangelogs\ChangelogsPlugin;

class ChangelogsPluginTest extends \PHPUnit_Framework_TestCase
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
    protected function setUp()
    {
        $this->tempDir = __DIR__ . '/temp';
        $this->config = new Config(false, realpath(__DIR__ . '/fixtures/local'));
        $this->config->merge(array(
            'config' => array(
                'home' => __DIR__,
            ),
        ));

        $this->io = new BufferIO();

        $this->composer = new Composer();
        $this->composer->setConfig($this->config);
        $this->composer->setPackage(new RootPackage('my/project', '1.0.0', '1.0.0'));
        $this->composer->setPluginManager(new PluginManager($this->io, $this->composer));
        $this->composer->setEventDispatcher(new EventDispatcher($this->composer, $this->io));

        self::cleanTempDir();
        mkdir($this->tempDir);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
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

    public function test_it_is_registered_and_activated()
    {
        $plugin = new ChangelogsPlugin();

        $this->addComposerPlugin($plugin);

        $this->assertSame(array($plugin), $this->composer->getPluginManager()->getPlugins());
        $this->assertAttributeInstanceOf('Composer\IO\IOInterface', 'io', $plugin);
        $this->assertAttributeInstanceOf('Pyrech\ComposerChangelogs\Outputter', 'outputter', $plugin);
    }

    public function test_it_receives_event()
    {
        $this->addComposerPlugin(new ChangelogsPlugin());

        $operation = $this->getUpdateOperation();

        $this->composer->getEventDispatcher()->dispatchPackageEvent(
            PackageEvents::POST_PACKAGE_UPDATE,
            false,
            new DefaultPolicy(false, false),
            new Pool(),
            new CompositeRepository(array()),
            new Request(new Pool()),
            array($operation),
            $operation
        );

        $this->composer->getEventDispatcher()->dispatchScript(ScriptEvents::POST_UPDATE_CMD);

        $expectedOutput = <<<OUTPUT
Changelogs summary:

 - foo/bar updated from v1.0.0 to v1.0.1
   See changes: https://github.com/foo/bar/compare/v1.0.0...v1.0.1
   Release notes: https://github.com/foo/bar/releases/tag/v1.0.1


OUTPUT;

        $this->assertSame($expectedOutput, $this->io->getOutput());
    }

    public function test_events_are_handled()
    {
        $plugin = new ChangelogsPlugin();
        $plugin->activate($this->composer, $this->io);

        $operation = $this->getUpdateOperation();

        $packageEvent = new PackageEvent(
            PackageEvents::POST_PACKAGE_UPDATE,
            $this->composer,
            $this->io,
            false,
            new DefaultPolicy(false, false),
            new Pool(),
            new CompositeRepository(array()),
            new Request(new Pool()),
            array($operation),
            $operation
        );
        $plugin->postPackageOperation($packageEvent);

        $postUpdateEvent = new Event(ScriptEvents::POST_UPDATE_CMD, $this->composer, $this->io);
        $plugin->postUpdate($postUpdateEvent);

        $expectedOutput = <<<OUTPUT
Changelogs summary:

 - foo/bar updated from v1.0.0 to v1.0.1
   See changes: https://github.com/foo/bar/compare/v1.0.0...v1.0.1
   Release notes: https://github.com/foo/bar/releases/tag/v1.0.1


OUTPUT;

        $this->assertSame($expectedOutput, $this->io->getOutput());
    }

    public function test_it_commits_with_always_option()
    {
        $this->config->merge(array(
            'config' => array(
                'home' => realpath(__DIR__ . '/fixtures/home'),
            ),
        ));

        $plugin = new ChangelogsPlugin();
        $plugin->activate($this->composer, $this->io);

        $operation = $this->getUpdateOperation();

        $packageEvent = new PackageEvent(
            PackageEvents::POST_PACKAGE_UPDATE,
            $this->composer,
            $this->io,
            false,
            new DefaultPolicy(false, false),
            new Pool(),
            new CompositeRepository(array()),
            new Request(new Pool()),
            array($operation),
            $operation
        );
        $plugin->postPackageOperation($packageEvent);

        $postUpdateEvent = new Event(ScriptEvents::POST_UPDATE_CMD, $this->composer, $this->io);
        $plugin->postUpdate($postUpdateEvent);

        $this->assertStringMatchesFormat('%aExecuting following command: %s/tests/fixtures/bin/fake.sh \'%s\' \'%s/composer-changelogs-%s\'', $this->io->getOutput());
    }

    public function test_it_commits_with_default_commit_message()
    {
        $this->config->merge(array(
            'config' => array(
                'home' => realpath(__DIR__ . '/fixtures/home'),
            ),
        ));

        $plugin = new ChangelogsPlugin();
        $plugin->activate($this->composer, $this->io);

        $operation = $this->getUpdateOperation();

        $packageEvent = new PackageEvent(
            PackageEvents::POST_PACKAGE_UPDATE,
            $this->composer,
            $this->io,
            false,
            new DefaultPolicy(false, false),
            new Pool(),
            new CompositeRepository(array()),
            new Request(new Pool()),
            array($operation),
            $operation
        );
        $plugin->postPackageOperation($packageEvent);

        $postUpdateEvent = new Event(ScriptEvents::POST_UPDATE_CMD, $this->composer, $this->io);
        $plugin->postUpdate($postUpdateEvent);

        $this->assertFileExists($this->tempDir . '/commit-message.txt');
        $commitMessage = file_get_contents($this->tempDir . '/commit-message.txt');
        $this->assertStringMatchesFormat('Update dependencies%aChangelogs summary:%a', $commitMessage);
    }

    public function test_it_commits_with_custom_commit_message()
    {
        $this->config->merge(array(
            'config' => array(
                'home' => realpath(__DIR__ . '/fixtures/home-commit-message'),
            ),
        ));

        $plugin = new ChangelogsPlugin();
        $plugin->activate($this->composer, $this->io);

        $operation = $this->getUpdateOperation();

        $packageEvent = new PackageEvent(
            PackageEvents::POST_PACKAGE_UPDATE,
            $this->composer,
            $this->io,
            false,
            new DefaultPolicy(false, false),
            new Pool(),
            new CompositeRepository(array()),
            new Request(new Pool()),
            array($operation),
            $operation
        );
        $plugin->postPackageOperation($packageEvent);

        $postUpdateEvent = new Event(ScriptEvents::POST_UPDATE_CMD, $this->composer, $this->io);
        $plugin->postUpdate($postUpdateEvent);

        $this->assertFileExists($this->tempDir . '/commit-message.txt');
        $commitMessage = file_get_contents($this->tempDir . '/commit-message.txt');
        $this->assertStringMatchesFormat('chore: Update composer%aChangelogs summary:%a', $commitMessage);
    }

    private function addComposerPlugin(PluginInterface $plugin)
    {
        $pluginManagerReflection = new \ReflectionClass($this->composer->getPluginManager());
        $addPluginReflection = $pluginManagerReflection->getMethod('addPlugin');
        $addPluginReflection->setAccessible(true);
        $addPluginReflection->invoke($this->composer->getPluginManager(), $plugin);
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
}
