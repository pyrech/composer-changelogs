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

class GitlabUrlGenerator extends GitBasedUrlGenerator
{
    private string $host;

    public function __construct(string $host)
    {
        $this->host = $host;
    }

    protected function getDomain(): string
    {
        return $this->host;
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

        if ($sourceUrlFrom !== $sourceUrlTo) {
            // Comparison across forks is not supported
            return false;
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
            '%s/tags/%s',
            $this->generateBaseUrl($sourceUrl),
            $version->getPretty()
        );
    }
}
