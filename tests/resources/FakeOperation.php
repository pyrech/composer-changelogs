<?php

/**
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\resources;

use Composer\DependencyResolver\Operation\OperationInterface;

class FakeOperation implements OperationInterface
{
    /** @var string */
    private $text;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * {@inheritdoc}
     */
    public function getJobType()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getReason()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return '';
    }
}
