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

use Pyrech\ComposerChangelogs\Version;

class BitbucketUrlGenerator extends AbstractUrlGenerator
{
    const DOMAIN = 'bitbucket.org';
    const URL_REGEX = '@bitbucket.org/(?P<user>[^/]+)/(?P<repository>[^/]+)@';
    const SSH_REGEX = '/^git@bitbucket\.org:(?P<user>[^\/]+)\/(?P<repository>.+)\.git$/';

    /**
     * {@inheritdoc}
     */
    public function supports($sourceUrl)
    {
        return strpos($sourceUrl, self::DOMAIN) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCompareUrl($sourceUrlFrom, Version $versionFrom, $sourceUrlTo, Version $versionTo)
    {
        // Check if both urls come from the supported domain
        // It avoids problems when one url is from another domain or is local
        if (!$this->supports($sourceUrlFrom) || !$this->supports($sourceUrlTo)) {
            return false;
        }

        $sourceUrlFrom = $this->generateBaseUrl($this->reformatSshUrl($sourceUrlFrom));
        $sourceUrlTo = $this->generateBaseUrl($this->reformatSshUrl($sourceUrlTo));

        // Check if comparison across forks is needed
        if ($sourceUrlFrom !== $sourceUrlTo) {
            $repositoryFrom = $this->extractRepositoryInformation($sourceUrlFrom);
            $repositoryTo = $this->extractRepositoryInformation($sourceUrlTo);

            return sprintf(
                '%s/branches/compare/%s/%s:%s%%0D%s/%s:%s',
                $sourceUrlTo,
                $repositoryTo['user'],
                $repositoryTo['repository'],
                $this->getCompareVersion($versionTo),
                $repositoryFrom['user'],
                $repositoryFrom['repository'],
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

    /**
     * {@inheritdoc}
     */
    public function generateReleaseUrl($sourceUrl, Version $version)
    {
        // Releases are not supported on Bitbucket :'(
        return false;
    }

    /**
     * @param string $sourceUrl
     *
     * @return array
     */
    private function extractRepositoryInformation($sourceUrl)
    {
        preg_match(self::URL_REGEX, $sourceUrl, $matches);

        if (!isset($matches['user']) || !isset($matches['repository'])) {
            throw new \LogicException(
                sprintf('Malformed Bitbucket source url: "%s"', $sourceUrl)
            );
        }

        return [
            'user' => $matches['user'],
            'repository' => $matches['repository'],
        ];
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function reformatSshUrl($url)
    {
        if (preg_match(self::SSH_REGEX, $url, $matches)) {
            return sprintf(
                'https://bitbucket.org/%s/%s',
                $matches['user'],
                $matches['repository']
            );
        }

        return $url;
    }
}
