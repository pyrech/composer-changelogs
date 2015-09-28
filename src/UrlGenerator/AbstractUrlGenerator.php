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

abstract class AbstractUrlGenerator implements UrlGenerator
{
    /**
     * Generates the base url for a repository by removing the .git part.
     *
     * @param Update $update
     *
     * @return string
     */
    protected function generateBaseUrl(Update $update)
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
