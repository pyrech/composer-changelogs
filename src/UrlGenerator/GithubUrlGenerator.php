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

class GithubUrlGenerator extends AbstractUrlGenerator
{
    const DOMAIN = 'github.com';
    const URL_REGEX = '@github.com/(?P<user>[^/]+)/(?P<repository>[^/]+)@';

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
        $sourceUrlFrom = $this->generateBaseUrl($sourceUrlFrom);
        $sourceUrlTo = $this->generateBaseUrl($sourceUrlTo);

        // Check if comparison across forks is needed
        if ($sourceUrlFrom !== $sourceUrlTo) {
            $userFrom = $this->extractUser($sourceUrlFrom);
            $userTo = $this->extractUser($sourceUrlTo);

            return sprintf(
                '%s/compare/%s:%s...%s:%s',
                $sourceUrlTo,
                $userFrom,
                $this->getCompareVersion($versionFrom),
                $userTo,
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

    /**
     * {@inheritdoc}
     */
    public function generateReleaseUrl($sourceUrl, Version $version)
    {
        if ($this->isDevVersion($version)) {
            return false;
        }

        return sprintf(
            '%s/releases/tag/%s',
            $this->generateBaseUrl($sourceUrl),
            $version->getPretty()
        );
    }

    /**
     * @param string $sourceUrl
     *
     * @¶eturn string
     */
    private function extractUser($sourceUrl)
    {
        preg_match(self::URL_REGEX, $sourceUrl, $matches);

        if (!isset($matches['user'])) {
            throw new \LogicException(
                sprintf('Malformed Github source url: "%s"', $sourceUrl)
            );
        }

        return $matches['user'];
    }
}
