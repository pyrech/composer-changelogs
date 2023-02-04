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

use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\Semver\Comparator;
use Pyrech\ComposerChangelogs\Model\Version;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class UpdateHandler implements OperationHandler
{
    public function supports(OperationInterface $operation): bool
    {
        return $operation instanceof UpdateOperation;
    }

    public function extractSourceUrl(OperationInterface $operation): ?string
    {
        if (!($operation instanceof UpdateOperation)) {
            throw new \LogicException('Operation should be an instance of UpdateOperation');
        }

        return $operation->getTargetPackage()->getSourceUrl();
    }

    public function getOutput(OperationInterface $operation, ?UrlGenerator $urlGenerator = null): array
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
            $initialPackage->getFullPrettyVersion()
        );
        $versionTo = new Version(
            $targetPackage->getVersion(),
            $targetPackage->getPrettyVersion(),
            $targetPackage->getFullPrettyVersion()
        );

        $action = 'updated';

        if (Comparator::greaterThan($versionFrom->getName(), $versionTo->getName())) {
            $action = 'downgraded';
        }

        $output[] = sprintf(
            ' - <fg=green>%s</fg=green> %s from <fg=yellow>%s</fg=yellow> to <fg=yellow>%s</fg=yellow>%s',
            $initialPackage->getName(),
            $action,
            $versionFrom->getCliOutput(),
            $versionTo->getCliOutput(),
            $this->getSemverOutput($versionFrom->getName(), $versionTo->getName())
        );

        if ($urlGenerator) {
            $compareUrl = $urlGenerator->generateCompareUrl(
                $initialPackage->getSourceUrl(),
                $versionFrom,
                $targetPackage->getSourceUrl(),
                $versionTo
            );

            if (!empty($compareUrl)) {
                $output[] = sprintf(
                    '   See changes: %s',
                    $compareUrl
                );
            }

            $releaseUrl = $urlGenerator->generateReleaseUrl(
                $this->extractSourceUrl($operation),
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

    private function getSemverOutput(string $versionFrom, string $versionTo): string
    {
        if (false === strpos($versionFrom, '.') && false === strpos($versionTo, '.')) {
            return '';
        }

        $versionsFrom = \explode('.', $versionFrom);
        $versionsTo = \explode('.', $versionTo);

        if (version_compare($versionsFrom[0], $versionsTo[0], '!=')) {
            return ' <fg=red>major</>';
        }

        if (version_compare($versionsFrom[0], $versionsTo[0], '==') && version_compare($versionsFrom[1], $versionsTo[1], '!=')) {
            return ' <fg=magenta>minor</>';
        }

        return ' <fg=cyan>patch</>';
    }
}
