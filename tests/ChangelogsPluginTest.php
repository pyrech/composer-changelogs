<?php

/**
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests;

use Composer\Composer;
use Composer\DependencyResolver\DefaultPolicy;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Request;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Installer\PackageEvents;
use Composer\IO\BufferIO;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Composer\Plugin\PluginManager;
use Composer\Repository\CompositeRepository;
use Composer\Script\ScriptEvents;
use Pyrech\ComposerChangelogs\ChangelogsPlugin;

class ChangelogsPluginTest extends \PHPUnit_Framework_TestCase
{
    /** @var BufferIO */
    private $io;

    /** @var Composer */
    private $composer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->io = new BufferIO();
        $this->composer = new Composer();
        $this->composer->setPackage(new RootPackage('my/project', '1.0.0', '1.0.0'));
        $this->composer->setPluginManager(new PluginManager($this->io, $this->composer));
        $this->composer->setEventDispatcher(new EventDispatcher($this->composer, $this->io));
    }

    public function test_it_is_registered_and_activated()
    {
        $plugin = new ChangelogsPlugin();
        $this->composer->getPluginManager()->addPlugin($plugin);
        $this->assertSame([$plugin], $this->composer->getPluginManager()->getPlugins());
        $this->assertAttributeInstanceOf('Composer\IO\IOInterface', 'io', $plugin);
        $this->assertAttributeInstanceOf('Pyrech\ComposerChangelogs\Outputter', 'outputter', $plugin);
    }

    public function test_it_receives_event()
    {
        $this->composer->getPluginManager()->addPlugin(new ChangelogsPlugin());

        $initialPackage = new Package('foo/bar', '1.0.0.0', 'v1.0.0');
        $initialPackage->setSourceUrl('https://github.com/foo/bar.git');

        $targetPackage = new Package('foo/bar', '1.0.1.0', 'v1.0.1');
        $targetPackage->setSourceUrl('https://github.com/foo/bar.git');

        $operation = new UpdateOperation($initialPackage, $targetPackage);

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

        $this->composer->getEventDispatcher()->dispatchScript(ScriptEvents::POST_UPDATE_CMD);

        $expectedOutput = <<<OUTPUT
Changelogs summary:

 - foo/bar updated from v1.0.0 to v1.0.1
   See changes: https://github.com/foo/bar/compare/v1.0.0...v1.0.1
   Release notes: https://github.com/foo/bar/releases/tag/v1.0.1


OUTPUT;

        $this->assertSame($expectedOutput, $this->io->getOutput());
    }
}
