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
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

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

        $versionFrom = $initialPackage->getPrettyVersion();
        $versionTo = $targetPackage->getPrettyVersion();

        $output[] = sprintf(
            ' - <fg=green>%s</fg=green> updated from <fg=yellow>%s</fg=yellow> to <fg=yellow>%s</fg=yellow>',
            $initialPackage->getName(),
            $versionFrom,
            $versionTo
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
