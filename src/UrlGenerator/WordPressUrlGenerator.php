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
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
class WordPressUrlGenerator implements UrlGenerator
{
    public const DOMAIN = 'svn.wordpress.org';

    /**
     * {@inheritdoc}
     */
    public function supports($sourceUrl)
    {
        return $sourceUrl && false !== strpos($sourceUrl, self::DOMAIN);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCompareUrl($sourceUrlFrom, Version $versionFrom, $sourceUrlTo, Version $versionTo)
    {
        if (!$sourceUrlTo) {
            return false;
        }

        if (preg_match('#plugins.svn.wordpress.org/(.*)/#', $sourceUrlTo, $matches)) {
            $plugin = $matches[1];

            return sprintf('https://wordpress.org/plugins/%s/changelog/', $plugin);
        }

        if (preg_match('#themes.svn.wordpress.org/(.*)/#', $sourceUrlTo, $matches)) {
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
