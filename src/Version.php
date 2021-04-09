<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs;

class Version
{
    /** @var string */
    private $name;

    /** @var string */
    private $pretty;

    /** @var string */
    private $fullPretty;

    /**
     * @param string $name
     * @param string $pretty
     * @param string $fullPretty
     */
    public function __construct($name, $pretty, $fullPretty)
    {
        $this->name = $name;
        $this->pretty = $pretty;
        $this->fullPretty = $fullPretty;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPretty()
    {
        return $this->pretty;
    }

    /**
     * @return string
     */
    public function getFullPretty()
    {
        return $this->fullPretty;
    }

    /**
     * Return whether the version is dev or not.
     *
     * @return string
     */
    public function isDev()
    {
        return 'dev-' === substr($this->name, 0, 4) || '-dev' === substr($this->name, -4);
    }

    /**
     * Return the version string for CLI Output
     * In case of dev version it adds the vcs hash.
     *
     * @return string
     */
    public function getCliOutput()
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
