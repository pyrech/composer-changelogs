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

use Pyrech\ComposerChangelogs\OperationHandler\OperationHandler;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class Factory
{
    /**
     * @return OperationHandler[]
     */
    public static function createOperationHandlers()
    {
        return [
            new \Pyrech\ComposerChangelogs\OperationHandler\InstallHandler(),
            new \Pyrech\ComposerChangelogs\OperationHandler\UpdateHandler(),
            new \Pyrech\ComposerChangelogs\OperationHandler\UninstallHandler(),
        ];
    }

    /**
     * @return UrlGenerator[]
     */
    public static function createUrlGenerators()
    {
        return [
            new \Pyrech\ComposerChangelogs\UrlGenerator\GithubUrlGenerator(),
            new \Pyrech\ComposerChangelogs\UrlGenerator\BitbucketUrlGenerator(),
            new \Pyrech\ComposerChangelogs\UrlGenerator\WordPressUrlGenerator(),
        ];
    }

    /**
     * @return Outputter
     */
    public static function createOutputter()
    {
        return new Outputter(
            self::createOperationHandlers(),
            self::createUrlGenerators()
        );
    }
}
