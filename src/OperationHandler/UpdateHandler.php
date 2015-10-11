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
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\Package\Version\VersionParser;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;
use Pyrech\ComposerChangelogs\Version;

class UpdateHandler implements OperationHandler
{
    /**
     * {@inheritdoc}
     */
    public function supports(OperationInterface $operation)
    {
        return $operation instanceof UpdateOperation;
    }

    /**
     * {@inheritdoc}
     */
    public function extractSourceUrl(OperationInterface $operation)
    {
        if (!($operation instanceof UpdateOperation)) {
            throw new \LogicException('Operation should be an instance of UpdateOperation');
        }

        return $operation->getTargetPackage()->getSourceUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(OperationInterface $operation, UrlGenerator $urlGenerator = null)
    {
        if (!($operation instanceof UpdateOperation)) {
            throw new \LogicException('Operation should be an instance of UpdateOperation');
        }

        $output = [];

        $initialPackage = $operation->getInitialPackage();
        $targetPackage = $operation->getTargetPackage();

        $versionFrom = new Version(
            $initialPackage->getVersion(),
            $initialPackage->getPrettyVersion(),
            method_exists($initialPackage, 'getFullPrettyVersion') // This method was added after composer v1.0.0-alpha10
                ? $initialPackage->getFullPrettyVersion()
                : VersionParser::formatVersion($initialPackage)
        );
        $versionTo = new Version(
            $targetPackage->getVersion(),
            $targetPackage->getPrettyVersion(),
            method_exists($targetPackage, 'getFullPrettyVersion') // This method was added after composer v1.0.0-alpha10
                ? $targetPackage->getFullPrettyVersion()
                : VersionParser::formatVersion($targetPackage)
        );

        $output[] = sprintf(
            ' - <fg=green>%s</fg=green> updated from <fg=yellow>%s</fg=yellow> to <fg=yellow>%s</fg=yellow>',
            $initialPackage->getName(),
            $versionFrom->getPretty(),
            $versionTo->getPretty()
        );

        if ($urlGenerator) {
            $compareUrl = $urlGenerator->generateCompareUrl(
                $initialPackage->getSourceUrl(),
                $versionFrom,
                $versionTo
            );

            if (!empty($compareUrl)) {
                $output[] = sprintf(
                    '   See changes: %s',
                    $compareUrl
                );
            }

            $releaseUrl = $urlGenerator->generateReleaseUrl(
                $initialPackage->getSourceUrl(),
                $versionTo
            );

            if (!empty($releaseUrl)) {
                $output[] = sprintf(
                    '   Release notes: %s',
                    $releaseUrl
                );
            }
        }

        return $output;
    }
}
