<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\UrlGenerator;

use Pyrech\ComposerChangelogs\Model\Version;

class GithubUrlGenerator extends GitBasedUrlGenerator
{
    protected function getDomain(): string
    {
        return 'github.com';
    }

    public function generateCompareUrl(?string $sourceUrlFrom, Version $versionFrom, ?string $sourceUrlTo, Version $versionTo)
    {
        // Check if both urls come from the supported domain
        // It avoids problems when one url is from another domain or is local
        if (!$this->supports($sourceUrlFrom) || !$this->supports($sourceUrlTo)) {
            return false;
        }

        $sourceUrlFrom = $this->generateBaseUrl($sourceUrlFrom);
        $sourceUrlTo = $this->generateBaseUrl($sourceUrlTo);

        // Check if comparison across forks is needed
        if ($sourceUrlFrom !== $sourceUrlTo) {
            $repositoryFrom = $this->extractRepositoryInformation($sourceUrlFrom);
            $repositoryTo = $this->extractRepositoryInformation($sourceUrlTo);

            return sprintf(
                '%s/compare/%s:%s...%s:%s',
                $sourceUrlTo,
                $repositoryFrom->getUser(),
                $this->getCompareVersion($versionFrom),
                $repositoryTo->getUser(),
                $this->getCompareVersion($versionTo)
            );
        }

        return sprintf(
            '%s/compare/%s...%s',
            $sourceUrlTo,
            $this->getCompareVersion($versionFrom),
            $this->getCompareVersion($versionTo)
        );
    }

    public function generateReleaseUrl(?string $sourceUrl, Version $version)
    {
        if ($version->isDev()) {
            return false;
        }

        $baseUrl = $this->generateBaseUrl($sourceUrl);

        if (!$baseUrl) {
            return false;
        }

        return sprintf(
            '%s/releases/tag/%s',
            $baseUrl,
            $version->getPretty()
        );
    }
}
