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

use Pyrech\ComposerChangelogs\Update;

class GithubUrlGenerator implements UrlGenerator
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
    public function generateCompareUrl(Update $update)
    {
        return sprintf(
            '%s/compare/%s...%s',
            $this->generateBaseUrl($update),
            $update->getVersionFrom(),
            $update->getVersionTo()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateReleaseUrl(Update $update)
    {
        return sprintf(
            '%s/releases/tag/%s',
            $this->generateBaseUrl($update),
            $update->getVersionTo()
        );
    }

    /**
     * @param Update $update
     *
     * @return string
     */
    private function generateBaseUrl(Update $update)
    {
        $sourceUrl = parse_url($update->getSourceUrl());
        $pos = strrpos($sourceUrl['path'], '.git');

        return sprintf(
            '%s://%s%s',
            $sourceUrl['scheme'],
            $sourceUrl['host'],
            $pos === false ? $sourceUrl['path'] : substr($sourceUrl['path'], 0, strrpos($sourceUrl['path'], '.git'))
        );
    }
}
