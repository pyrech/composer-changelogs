<?php

/**
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs;

class Update
{
    /** @var string */
    private $packageName;

    /** @var string */
    private $versionFrom;

    /** @var string */
    private $versionTo;

    /** @var string */
    private $sourceUrl;

    /**
     * @param string $packageName
     * @param string $versionFrom
     * @param string $versionTo
     */
    public function __construct($packageName, $versionFrom, $versionTo, $sourceUrl)
    {
        $this->packageName = $packageName;
        $this->versionFrom = $versionFrom;
        $this->versionTo = $versionTo;
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getVersionFrom()
    {
        return $this->versionFrom;
    }

    /**
     * @return string
     */
    public function getVersionTo()
    {
        return $this->versionTo;
    }

    /**
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }
}
