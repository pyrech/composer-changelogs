<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\resources;

use Pyrech\ComposerChangelogs\Model\Version;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class FakeUrlGenerator implements UrlGenerator
{
    private bool $supports;

    /** @var string|false */
    private $compareUrl;

    /** @var string|false */
    private $releaseUrl;

    /**
     * @param string|false $compareUrl
     * @param string|false $releaseUrl
     */
    public function __construct(bool $supports, $compareUrl, $releaseUrl)
    {
        $this->supports = $supports;
        $this->compareUrl = $compareUrl;
        $this->releaseUrl = $releaseUrl;
    }

    public function supports($sourceUrl): bool
    {
        return $this->supports;
    }

    public function generateCompareUrl($sourceUrlFrom, Version $versionFrom, $sourceUrlTo, Version $versionTo)
    {
        return $this->compareUrl;
    }

    public function generateReleaseUrl($sourceUrl, Version $version)
    {
        return $this->releaseUrl;
    }
}
