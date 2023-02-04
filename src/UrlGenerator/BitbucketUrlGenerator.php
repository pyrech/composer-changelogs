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

class BitbucketUrlGenerator extends GitBasedUrlGenerator
{
    protected function getDomain(): string
    {
        return 'bitbucket.org';
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
                '%s/branches/compare/%s/%s:%s%%0D%s/%s:%s',
                $sourceUrlTo,
                $repositoryTo->getUser(),
                $repositoryTo->getName(),
                $this->getCompareVersion($versionTo),
                $repositoryFrom->getUser(),
                $repositoryFrom->getName(),
                $this->getCompareVersion($versionFrom)
            );
        }

        return sprintf(
            '%s/branches/compare/%s%%0D%s',
            $sourceUrlTo,
            $this->getCompareVersion($versionTo),
            $this->getCompareVersion($versionFrom)
        );
    }

    public function generateReleaseUrl(?string $sourceUrl, Version $version): bool
    {
        // Releases are not supported on Bitbucket :'(
        return false;
    }
}
