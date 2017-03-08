<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Pyrech\ComposerChangelogs\Config\Config;
use Pyrech\ComposerChangelogs\Config\ConfigBuilder;
use Pyrech\ComposerChangelogs\Config\ConfigLocator;

class ChangelogsPlugin implements PluginInterface, EventSubscriberInterface
{
    const EXTRA_KEY = 'composer-changelogs';

    /** @var Composer */
    private $composer;

    /** @var IOInterface */
    private $io;

    /** @var Outputter */
    private $outputter;

    /** @var ConfigLocator */
    private $configLocator;

    /** @var Config */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->configLocator = new ConfigLocator($composer);

        $this->setupConfig();
        $this->autoloadNeededClasses();

        $this->outputter = Factory::createOutputter($this->config->getGitlabHosts());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            PackageEvents::POST_PACKAGE_UPDATE => array(
                array('postPackageOperation'),
            ),
            PackageEvents::POST_PACKAGE_INSTALL => array(
                array('postPackageOperation'),
            ),
            PackageEvents::POST_PACKAGE_UNINSTALL => array(
                array('postPackageOperation'),
            ),
            ScriptEvents::POST_UPDATE_CMD => array(
                array('postUpdate'),
            ),
        );
    }

    /**
     * @param PackageEvent $event
     */
    public function postPackageOperation(PackageEvent $event)
    {
        $operation = $event->getOperation();

        $this->outputter->addOperation($operation);
    }

    /**
     * @param Event $event
     */
    public function postUpdate(Event $event)
    {
        $this->io->write($this->outputter->getOutput());

        $this->handleCommit();
    }

    /**
     * This method ensures all the classes required to make the plugin working
     * are loaded.
     *
     * It's required to avoid composer looking for classes no longer existing
     * (after the plugin is updated or removed for example).
     *
     * Lot of classes (like operation handlers, url generators, Outputter, etc)
     * do not need this because they are already autoloaded at the activation
     * of the plugin.
     */
    private function autoloadNeededClasses()
    {
        $classes = array(
            'Pyrech\ComposerChangelogs\Version',
        );

        foreach ($classes as $class) {
            // Force the class to be autoloaded
            class_exists($class, true);
        }
    }

    private function setupConfig()
    {
        $builder = new ConfigBuilder();

        $this->config = $builder->build(
            $this->configLocator->getConfig(self::EXTRA_KEY),
            $this->configLocator->getPath(self::EXTRA_KEY)
        );

        if (count($builder->getWarnings()) > 0) {
            $this->io->writeError('<error>Invalid config for composer-changelogs plugin:</error>');
            foreach ($builder->getWarnings() as $warning) {
                $this->io->write('    ' . $warning);
            }
        }
    }

    private function handleCommit()
    {
        if ($this->outputter->isEmpty()) {
            return;
        }

        switch ($this->config->getCommitAuto()) {
            case 'never':
                return;
            case 'ask':
                if ($this->io->askConfirmation('<info>Would you like to commit the update? </info>[<comment>no</comment>]: ', false)) {
                    $this->doCommit();
                }
                break;
            case 'always':
                $this->doCommit();
        }
    }

    private function doCommit()
    {
        if (!$this->config->getCommitBinFile()) {
            $this->io->writeError('<error>No "commit-bin-file" for composer-changelogs plugin. Commit not done.</error>');

            return;
        }

        $workingDirectory = getcwd();
        $filename = tempnam(sys_get_temp_dir(), 'composer-changelogs-');
        $message = $this->config->getCommitMessage() . PHP_EOL . PHP_EOL . strip_tags($this->outputter->getOutput());

        file_put_contents($filename, $message);

        $command = $this->config->getCommitBinFile() . ' ' . escapeshellarg($workingDirectory) . ' ' . escapeshellarg($filename);

        $this->io->write(sprintf('Executing following command: %s', $command));
        exec($command);
    }
}
