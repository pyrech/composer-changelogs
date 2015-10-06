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

interface UrlGenerator
{
    /**
     * @param string $sourceUrl
     *
     * @return bool
     */
    public function supports($sourceUrl);

    /**
     * Return the compare url for these versions or false if compare url is not
     * supported.
     *
     * @param string $sourceUrl
     * @param string $versionFrom
     * @param string $versionTo
     *
     * @return string|false
     */
    public function generateCompareUrl($sourceUrl, $versionFrom, $versionTo);

    /**
     * Return the release url for the given version or false if compare url is
     * not supported.
     *
     * @param string $sourceUrl
     * @param string $version
     *
     * @return string|false
     */
    public function generateReleaseUrl($sourceUrl, $version);
}
