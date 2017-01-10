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

/**
 * @deprecated since v1.4, will be removed in v2.0. Use GitBasedUrlGenerator class instead
 */
abstract class AbstractUrlGenerator extends GitBasedUrlGenerator
{
    /**
     * Return whether the version is dev or not.
     *
     * @param Version $version
     *
     * @return string
     *
     * @deprecated since v1.4, will be removed in v2.0. Use $version->isDev() instead
     */
    protected function isDevVersion(Version $version)
    {
        return $version->isDev();
    }
}
