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

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
class WordPressUrlGenerator implements UrlGenerator
{
    const DOMAIN = 'svn.wordpress.org';

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
        if (preg_match('#plugins.svn.wordpress.org/(.*)/#', $sourceUrl, $matches)) {
            $plugin = $matches[1];

            return sprintf('https://wordpress.org/plugins/%s/changelog/', $plugin);
        }

        if (preg_match('#themes.svn.wordpress.org/(.*)/#', $sourceUrl, $matches)) {
            $theme = $matches[1];

            return sprintf('https://themes.trac.wordpress.org/log/%s/', $theme);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function generateReleaseUrl($sourceUrl, Version $version)
    {
        return false;
    }
}
