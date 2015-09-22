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

interface UrlGenerator
{
    /**
     * @param string $sourceUrl
     *
     * @return bool
     */
    public function supports($sourceUrl);

    /**
     * Return the compare url for this update or false if compare url not supported.
     *
     * @param Update $update
     *
     * @return string|false
     */
    public function generateCompareUrl(Update $update);

    /**
     * Return the release url for this update or false if compare url not supported.
     *
     * @param Update $update
     *
     * @return string|false
     */
    public function generateReleaseUrl(Update $update);
}
