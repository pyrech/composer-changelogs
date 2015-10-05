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
}
