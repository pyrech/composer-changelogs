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

class BitbucketUrlGenerator extends AbstractUrlGenerator
{
    const DOMAIN = 'bitbucket.org';

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
    public function generateCompareUrl($sourceUrl, $versionFrom, $versionTo)
    {
        return sprintf(
            '%s/branches/compare/%s%%0D%s',
            $this->generateBaseUrl($sourceUrl),
            $versionTo,
            $versionFrom
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateReleaseUrl($sourceUrl, $version)
    {
        // Releases are not supported on Bitbucket :'(
        return false;
    }
}
