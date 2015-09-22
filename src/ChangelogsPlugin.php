<?php

/**
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs;

use Composer\Composer;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\PackageEvent;
use Composer\Script\ScriptEvents;
use Pyrech\ComposerChangelogs\UrlGenerator\GithubUrlGenerator;

class ChangelogsPlugin implements PluginInterface, EventSubscriberInterface
{
    /** @var IOInterface */
    private $io;

    /** @var Changelogs */
    private $changelogs;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->io = $io;
        $this->changelogs = new Changelogs([
            new GithubUrlGenerator(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_PACKAGE_UPDATE => array(
                array('postPackageUpdate'),
            ),
            ScriptEvents::POST_UPDATE_CMD => array(
                array('postUpdate'),
            ),
        );
    }

    /**
     * @param PackageEvent $event
     */
    public function postPackageUpdate(PackageEvent $event)
    {
        $operation = $event->getOperation();

        if (!$operation instanceof UpdateOperation) {
            return;
        }

        /** @var PackageInterface $initialPackage */
        $initialPackage = $operation->getInitialPackage();
        /** @var PackageInterface $targetPackage */
        $targetPackage = $operation->getTargetPackage();

        if ($initialPackage->getName() !== $targetPackage->getName()) {
            return;
        }

        $this->changelogs->addUpdate(new Update(
            $initialPackage->getName(),
            $initialPackage->getPrettyVersion(),
            $targetPackage->getPrettyVersion(),
            $targetPackage->getSourceUrl()
        ));
    }

    /**
     * @param Event $event
     */
    public function postUpdate(Event $event)
    {
        if ($this->changelogs->isEmpty()) {
            return;
        }

        $this->io->write($this->changelogs->getOutput());
    }
}
