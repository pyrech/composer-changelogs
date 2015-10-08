<?php

/**
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
    public function generateCompareUrl($sourceUrl, Version $versionFrom, Version $versionTo)
    {
        return sprintf(
            '%s/compare/%s...%s',
            $this->generateBaseUrl($sourceUrl),
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
}
