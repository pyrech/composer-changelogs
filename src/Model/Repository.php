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

class Repository
{
    private string $user;
    private string $name;

    public function __construct(string $user, string $name)
    {
        $this->user = $user;
        $this->name = $name;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
