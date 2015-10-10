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
     * @param string  $sourceUrl
     * @param Version $versionFrom
     * @param Version $versionTo
     *
     * @return string|false
     */
    public function generateCompareUrl($sourceUrl, Version $versionFrom, Version $versionTo);

    /**
     * Return the release url for the given version or false if compare url is
     * not supported.
     *
     * @param string  $sourceUrl
     * @param Version $version
     *
     * @return string|false
     */
    public function generateReleaseUrl($sourceUrl, Version $version);
}
