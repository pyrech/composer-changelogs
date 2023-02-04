<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\OperationHandler;

use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\OperationInterface;
use Pyrech\ComposerChangelogs\Model\Version;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class InstallHandler implements OperationHandler
{
    public function supports(OperationInterface $operation): bool
    {
        return $operation instanceof InstallOperation;
    }

    public function extractSourceUrl(OperationInterface $operation): ?string
    {
        if (!($operation instanceof InstallOperation)) {
            throw new \LogicException('Operation should be an instance of InstallOperation');
        }

        return $operation->getPackage()->getSourceUrl();
    }

    public function getOutput(OperationInterface $operation, ?UrlGenerator $urlGenerator = null): array
    {
        if (!($operation instanceof InstallOperation)) {
            throw new \LogicException('Operation should be an instance of InstallOperation');
        }

        $output = [];

        $package = $operation->getPackage();
        $version = new Version(
            $package->getVersion(),
            $package->getPrettyVersion(),
            $package->getFullPrettyVersion()
        );

        $output[] = sprintf(
            ' - <fg=green>%s</fg=green> installed in version <fg=yellow>%s</fg=yellow>',
            $package->getName(),
            $version->getCliOutput()
        );

        if ($urlGenerator) {
            $releaseUrl = $urlGenerator->generateReleaseUrl(
                $this->extractSourceUrl($operation),
                $version
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
