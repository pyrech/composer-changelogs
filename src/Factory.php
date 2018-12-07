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

class Factory
{
    /**
     * @return OperationHandler\OperationHandler[]
     */
    public static function createOperationHandlers()
    {
        return [
            new OperationHandler\InstallHandler(),
            new OperationHandler\UpdateHandler(),
            new OperationHandler\UninstallHandler(),
        ];
    }

    /**
     * @param string[] $gitlabHosts
     *
     * @return UrlGenerator\UrlGenerator[]
     */
    public static function createUrlGenerators(array $gitlabHosts = [])
    {
        $hosts = [
            new UrlGenerator\GithubUrlGenerator(),
            new UrlGenerator\BitbucketUrlGenerator(),
            new UrlGenerator\WordPressUrlGenerator(),
        ];

        foreach ($gitlabHosts as $gitlabHost) {
            $hosts[] = new UrlGenerator\GitlabUrlGenerator($gitlabHost);
        }

        return $hosts;
    }

    /**
     * @param string[] $gitlabHosts
     *
     * @return Outputter
     */
    public static function createOutputter(array $gitlabHosts = [])
    {
        return new Outputter(
            self::createOperationHandlers(),
            self::createUrlGenerators($gitlabHosts)
        );
    }
}
