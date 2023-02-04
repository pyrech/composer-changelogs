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

class Version
{
    private string $name;
    private string $pretty;
    private string $fullPretty;

    public function __construct(string $name, string $pretty, string $fullPretty)
    {
        $this->name = $name;
        $this->pretty = $pretty;
        $this->fullPretty = $fullPretty;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPretty(): string
    {
        return $this->pretty;
    }

    public function getFullPretty(): string
    {
        return $this->fullPretty;
    }

    /**
     * Return whether the version is dev or not.
     */
    public function isDev(): bool
    {
        return 'dev-' === substr($this->name, 0, 4) || '-dev' === substr($this->name, -4);
    }

    /**
     * Return the version string for CLI Output
     * In case of dev version it adds the vcs hash.
     */
    public function getCliOutput(): string
    {
        $cliOutput = $this->getPretty();
        if ($this->isDev()) {
            $hash = substr(
                $this->getFullPretty(),
                strlen($this->getPretty()) + 1
            );
            if ($hash) {
                $cliOutput .= '@' . $hash;
            }
        }

        return $cliOutput;
    }
}
