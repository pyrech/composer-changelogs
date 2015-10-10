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
}
