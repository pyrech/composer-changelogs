<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\UrlGenerator;

use Pyrech\ComposerChangelogs\Version;

abstract class GitBasedUrlGenerator implements UrlGenerator
{
    public const REGEX_USER = '(?P<user>[^/]+)';
    public const REGEX_REPOSITORY = '(?P<repository>[^/]+)';

    /**
     * Returns the domain of the service, like "example.org".
     *
     * @return string
     */
    abstract protected function getDomain();

    /**
     * {@inheritdoc}
     */
    public function supports($sourceUrl)
    {
        return $sourceUrl && false !== strpos($sourceUrl, $this->getDomain());
    }

    /**
     * Generates the canonical http url for a repository.
     *
     * It ensures there is no .git part in http url. It also supports ssh urls
     * by converting them in their http equivalent format.
     *
     * @param ?string $sourceUrl
     *
     * @return string
     */
    protected function generateBaseUrl($sourceUrl)
    {
        if (!$sourceUrl) {
            return '';
        }

        if ($this->isSshUrl($sourceUrl)) {
            return $this->transformSshUrlIntoHttp($sourceUrl);
        }

        $sourceUrl = parse_url($sourceUrl);
        $pos = strrpos($sourceUrl['path'], '.git');

        return sprintf(
            '%s://%s%s',
            $sourceUrl['scheme'],
            $sourceUrl['host'],
            false === $pos ? $sourceUrl['path'] : substr($sourceUrl['path'], 0, strrpos($sourceUrl['path'], '.git'))
        );
    }

    /**
     * Get the version to use for the compare url.
     *
     * For dev versions, it returns the commit short hash in full pretty version.
     *
     * @param Version $version
     *
     * @return string
     */
    protected function getCompareVersion(Version $version)
    {
        if ($version->isDev()) {
            return substr(
                $version->getFullPretty(),
                strlen($version->getPretty()) + 1
            );
        }

        return $version->getPretty();
    }

    /**
     * Extracts information like user and repository from the http url.
     *
     * @param string $sourceUrl
     *
     * @return array
     */
    protected function extractRepositoryInformation($sourceUrl)
    {
        $pattern = '#' . $this->getDomain() . '/' . self::REGEX_USER . '/' . self::REGEX_REPOSITORY . '#';

        preg_match($pattern, $sourceUrl, $matches);

        if (!isset($matches['user']) || !isset($matches['repository'])) {
            throw new \LogicException(
                sprintf('Unrecognized url format for %s ("%s")', $this->getDomain(), $sourceUrl)
            );
        }

        return [
            'user' => $matches['user'],
            'repository' => $matches['repository'],
        ];
    }

    /**
     * Returns whether an url uses a ssh git protocol.
     *
     * @param string $url
     *
     * @return string
     */
    private function isSshUrl($url)
    {
        return false !== strpos($url, 'git@');
    }

    /**
     * Transform an ssh git url into an http one.
     *
     * @param string $url
     *
     * @return string
     */
    private function transformSshUrlIntoHttp($url)
    {
        $pattern = '#git@' . $this->getDomain() . ':' . self::REGEX_USER . '/' . self::REGEX_REPOSITORY . '.git$#';

        if (preg_match($pattern, $url, $matches)) {
            return sprintf(
                'https://%s/%s/%s',
                $this->getDomain(),
                $matches['user'],
                $matches['repository']
            );
        }

        return $url;
    }
}
