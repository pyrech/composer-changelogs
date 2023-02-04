<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\Model;

class Config
{
    private string $commitAuto;
    private ?string $commitBinFile;
    private string $commitMessage;
    /** @var string[] */
    private array $gitlabHosts;
    private int $postUpdatePriority;

    /**
     * @param string[] $gitlabHosts
     */
    public function __construct(
        string $commitAuto,
        ?string $commitBinFile,
        string $commitMessage,
        array $gitlabHosts,
        int $postUpdatePriority)
    {
        $this->commitAuto = $commitAuto;
        $this->commitBinFile = $commitBinFile;
        $this->commitMessage = $commitMessage;
        $this->gitlabHosts = $gitlabHosts;
        $this->postUpdatePriority = $postUpdatePriority;
    }

    public function getCommitAuto(): string
    {
        return $this->commitAuto;
    }

    public function getCommitBinFile(): ?string
    {
        return $this->commitBinFile;
    }

    public function getCommitMessage(): string
    {
        return $this->commitMessage;
    }

    /**
     * @return string[]
     */
    public function getGitlabHosts(): array
    {
        return $this->gitlabHosts;
    }

    public function getPostUpdatePriority(): int
    {
        return $this->postUpdatePriority;
    }
}
