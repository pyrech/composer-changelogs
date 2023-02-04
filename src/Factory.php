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

use Pyrech\ComposerChangelogs\OperationHandler\InstallHandler;
use Pyrech\ComposerChangelogs\OperationHandler\UninstallHandler;
use Pyrech\ComposerChangelogs\OperationHandler\UpdateHandler;
use Pyrech\ComposerChangelogs\UrlGenerator\BitbucketUrlGenerator;
use Pyrech\ComposerChangelogs\UrlGenerator\GithubUrlGenerator;
use Pyrech\ComposerChangelogs\UrlGenerator\GitlabUrlGenerator;
use Pyrech\ComposerChangelogs\UrlGenerator\WordPressUrlGenerator;

class Factory
{
    /**
     * @return OperationHandler\OperationHandler[]
     */
    public static function createOperationHandlers(): array
    {
        return [
            new InstallHandler(),
            new UpdateHandler(),
            new UninstallHandler(),
        ];
    }

    /**
     * @param string[] $gitlabHosts
     *
     * @return UrlGenerator\UrlGenerator[]
     */
    public static function createUrlGenerators(array $gitlabHosts = []): array
    {
        $hosts = [
            new GithubUrlGenerator(),
            new BitbucketUrlGenerator(),
            new WordPressUrlGenerator(),
        ];

        foreach ($gitlabHosts as $gitlabHost) {
            $hosts[] = new GitlabUrlGenerator($gitlabHost);
        }

        return $hosts;
    }

    /**
     * @param string[] $gitlabHosts
     */
    public static function createOutputter(array $gitlabHosts = []): Outputter
    {
        return new Outputter(
            self::createOperationHandlers(),
            self::createUrlGenerators($gitlabHosts)
        );
    }
}
