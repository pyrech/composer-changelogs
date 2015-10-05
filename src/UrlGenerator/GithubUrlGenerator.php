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
    public function generateCompareUrl($sourceUrl, $versionFrom, $versionTo)
    {
        return sprintf(
            '%s/compare/%s...%s',
            $this->generateBaseUrl($sourceUrl),
            $versionFrom,
            $versionTo
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateReleaseUrl($sourceUrl, $version)
    {
        return sprintf(
            '%s/releases/tag/%s',
            $this->generateBaseUrl($sourceUrl),
            $version
        );
    }
}
