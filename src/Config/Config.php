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

    /** @var string[] */
    private $gitlabHosts;

    /**
     * @param string      $commitAuto
     * @param string|null $commitBinFile
     * @param string      $commitMessage
     * @param string[]    $gitlabHosts
     */
    public function __construct($commitAuto, $commitBinFile, $commitMessage, array $gitlabHosts)
    {
        $this->commitAuto = $commitAuto;
        $this->commitBinFile = $commitBinFile;
        $this->commitMessage = $commitMessage;
        $this->gitlabHosts = $gitlabHosts;
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

    /**
     * @return string[]
     */
    public function getGitlabHosts()
    {
        return $this->gitlabHosts;
    }
}
