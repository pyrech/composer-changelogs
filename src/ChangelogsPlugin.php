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
use Composer\Script\ScriptEvents;
use Pyrech\ComposerChangelogs\Config\Config;
use Pyrech\ComposerChangelogs\Config\ConfigBuilder;
use Pyrech\ComposerChangelogs\Config\ConfigLocator;

class ChangelogsPlugin implements PluginInterface, EventSubscriberInterface
{
    public const EXTRA_KEY = 'composer-changelogs';

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

    /** @var int */
    private static $postUpdatePriority = -1;

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

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_UPDATE => [
                ['postPackageOperation'],
            ],
            PackageEvents::POST_PACKAGE_INSTALL => [
                ['postPackageOperation'],
            ],
            PackageEvents::POST_PACKAGE_UNINSTALL => [
                ['postPackageOperation'],
            ],
            ScriptEvents::POST_UPDATE_CMD => [
                ['postUpdate', static::$postUpdatePriority],
            ],
        ];
    }

    /**
     * @param PackageEvent $event
     */
    public function postPackageOperation(PackageEvent $event)
    {
        $operation = $event->getOperation();

        $this->outputter->addOperation($operation);
    }

    public function postUpdate()
    {
        $this->io->write($this->outputter->getOutput());

        $this->handleCommit();
    }

    /**
     * This method ensures all the classes required to make the plugin work
     * are loaded.
     *
     * It's required to avoid composer looking for classes which no longer exist
     * (for example after the plugin is updated).
     *
     * Lot of classes (like operation handlers, url generators, Outputter, etc)
     * do not need this because they are already autoloaded at the activation
     * of the plugin.
     */
    private function autoloadNeededClasses()
    {
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if ('.php' === substr($file, 0, -4)) {
                class_exists(__NAMESPACE__ . str_replace('/', '\\', substr($file, \strlen(__DIR__), -4)));
            }
        }
    }

    private function setupConfig()
    {
        $builder = new ConfigBuilder();

        $this->config = $builder->build(
            $this->configLocator->getConfig(self::EXTRA_KEY),
            $this->configLocator->getPath(self::EXTRA_KEY)
        );

        static::$postUpdatePriority = $this->config->getPostUpdatePriority();

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
