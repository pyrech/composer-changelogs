<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\Config;

class Config
{
    /** @var string */
    private $commitAuto;

    /** @var string|null */
    private $commitBinFile;

    /**
     * @param string      $commitAuto
     * @param string|null $commitBinFile
     */
    public function __construct($commitAuto, $commitBinFile)
    {
        $this->commitAuto = $commitAuto;
        $this->commitBinFile = $commitBinFile;
    }

    /**
     * @return string
     */
    public function getCommitAuto()
    {
        return $this->commitAuto;
    }

    /**
     * @return string|null
     */
    public function getCommitBinFile()
    {
        return $this->commitBinFile;
    }
}
