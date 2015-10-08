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

abstract class AbstractUrlGenerator implements UrlGenerator
{
    /**
     * Generates the base url for a repository by removing the .git part.
     *
     * @param string $sourceUrl
     *
     * @return string
     */
    protected function generateBaseUrl($sourceUrl)
    {
        $sourceUrl = parse_url($sourceUrl);
        $pos = strrpos($sourceUrl['path'], '.git');

        return sprintf(
            '%s://%s%s',
            $sourceUrl['scheme'],
            $sourceUrl['host'],
            $pos === false ? $sourceUrl['path'] : substr($sourceUrl['path'], 0, strrpos($sourceUrl['path'], '.git'))
        );
    }

    /**
     * Return whether the version is dev or not.
     *
     * @param Version $version
     *
     * @return string
     */
    protected function isDevVersion(Version $version)
    {
        return substr($version->getName(), -4) === '-dev';
    }

    /**
     * Get the version to use for the compare url.
     *
     * For dev versions, it returns the commit short hash in full pretty version.
     *
     * @param Version $version
     *
     * @return string
     */
    protected function getCompareVersion(Version $version)
    {
        if ($this->isDevVersion($version)) {
            return substr(
                $version->getFullPretty(),
                strlen($version->getPretty()) + 1
            );
        }

        return $version->getPretty();
    }
}
