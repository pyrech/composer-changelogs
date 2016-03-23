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

    /** @var string */
    private $commitMessage;

    /**
     * @param string      $commitAuto
     * @param string|null $commitBinFile
     * @param string      $commitMessage
     */
    public function __construct($commitAuto, $commitBinFile, $commitMessage)
    {
        $this->commitAuto = $commitAuto;
        $this->commitBinFile = $commitBinFile;
        $this->commitMessage = $commitMessage;
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

    /**
     * @return string
     */
    public function getCommitMessage()
    {
        return $this->commitMessage;
    }
}
