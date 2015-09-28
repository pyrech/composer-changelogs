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
    public function generateCompareUrl(Update $update)
    {
        return sprintf(
            '%s/branches/compare/%s%%0D%s',
            $this->generateBaseUrl($update),
            $update->getVersionTo(),
            $update->getVersionFrom()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateReleaseUrl(Update $update)
    {
        // Releases are not supported on Bitbucket :'(
        return false;
    }
}
