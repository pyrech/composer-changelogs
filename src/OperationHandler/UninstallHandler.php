<?php

/**
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\OperationHandler;

use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\Package\Version\VersionParser;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;
use Pyrech\ComposerChangelogs\Version;

class UninstallHandler implements OperationHandler
{
    /**
     * {@inheritdoc}
     */
    public function supports(OperationInterface $operation)
    {
        return $operation instanceof UninstallOperation;
    }

    /**
     * {@inheritdoc}
     */
    public function extractSourceUrl(OperationInterface $operation)
    {
        if (!($operation instanceof UninstallOperation)) {
            throw new \LogicException('Operation should be an instance of UninstallOperation');
        }

        return $operation->getPackage()->getSourceUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(OperationInterface $operation, UrlGenerator $urlGenerator = null)
    {
        if (!($operation instanceof UninstallOperation)) {
            throw new \LogicException('Operation should be an instance of UninstallOperation');
        }

        $output = [];

        $package = $operation->getPackage();
        $version = new Version(
            $package->getVersion(),
            $package->getPrettyVersion(),
            method_exists($package, 'getFullPrettyVersion') // This method was added after composer v1.0.0-alpha10
                ? $package->getFullPrettyVersion()
                : VersionParser::formatVersion($package)
        );

        $output[] = sprintf(
            ' - <fg=green>%s</fg=green> removed (installed version was <fg=yellow>%s</fg=yellow>)',
            $package->getName(),
            $version->getPretty()
        );

        return $output;
    }
}
