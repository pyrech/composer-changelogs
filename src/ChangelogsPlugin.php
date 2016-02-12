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

class ChangelogsPlugin implements PluginInterface, EventSubscriberInterface
{
    /** @var IOInterface */
    private $io;

    /** @var Outputter */
    private $outputter;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->io = $io;
        $this->outputter = Factory::createOutputter();

        $this->autoloadNeededClasses();
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
                ['postUpdate'],
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

    /**
     * @param Event $event
     */
    public function postUpdate(Event $event)
    {
        $this->io->write($this->outputter->getOutput());
    }

    /**
     * This method ensures all the classes required to make the plugin working
     * are loaded.
     *
     * It's required to avoid composer looking for classes no longer existing
     * (after the plugin is updated or removed for example).
     *
     * All operation handlers, url generators and Outputter classes do not
     * need this because they are already autoloaded by the Factory.
     */
    private function autoloadNeededClasses()
    {
        $classes = [
            'Pyrech\ComposerChangelogs\Version',
        ];

        foreach ($classes as $class) {
            // Force the class to be autoloaded
            class_exists($class, true);
        }
    }
}
