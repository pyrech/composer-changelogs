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

use Composer\DependencyResolver\Operation\OperationInterface;

class FakeOperation implements OperationInterface
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getOperationType()
    {
        return '';
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function show($lock)
    {
        return '';
    }

    public function __toString()
    {
        return '';
    }
}
