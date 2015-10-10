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
    public function generateCompareUrl($sourceUrl, Version $versionFrom, Version $versionTo)
    {
        return sprintf(
            '%s/branches/compare/%s%%0D%s',
            $this->generateBaseUrl($sourceUrl),
            $this->getCompareVersion($versionTo),
            $this->getCompareVersion($versionFrom)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateReleaseUrl($sourceUrl, Version $version)
    {
        // Releases are not supported on Bitbucket :'(
        return false;
    }
}
