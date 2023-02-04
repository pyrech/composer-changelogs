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

interface UrlGenerator
{
    public function supports(?string $sourceUrl): bool;

    /**
     * Return the compare url for these versions or false if compare url is not
     * supported.
     *
     * In case the from and to source urls are different, this probably means
     * that an across fork compare url should be generated instead.
     *
     * @return string|false
     */
    public function generateCompareUrl(?string $sourceUrlFrom, Version $versionFrom, ?string $sourceUrlTo, Version $versionTo);

    /**
     * Return the release url for the given version or false if compare url is
     * not supported.
     *
     * @return string|false
     */
    public function generateReleaseUrl(?string $sourceUrl, Version $version);
}
