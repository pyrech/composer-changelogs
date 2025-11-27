<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\Config;

use Composer\Composer;

class ConfigLocator
{
    private Composer $composer;

    /** @var array<string, mixed> */
    private array $cache = [];

    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(string $key): array
    {
        $this->locate($key);

        return $this->cache[$key]['config'];
    }

    public function getPath(string $key): ?string
    {
        $this->locate($key);

        return $this->cache[$key]['path'];
    }

    /**
     * Try to locate where is the config for the given key.
     */
    public function locate(string $key): bool
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key]['found'];
        }

        if ($this->locateLocal($key)) {
            return true;
        }

        if ($this->locateGlobal($key)) {
            return true;
        }

        $this->cache[$key] = [
            'found' => false,
            'config' => [],
            'path' => null,
        ];

        return false;
    }

    /**
     * Search config in the local root package.
     */
    private function locateLocal(string $key): bool
    {
        $composerConfig = $this->composer->getConfig();

        // Sorry for this, I couldn't find any way to get the path of the current root package
        $reflection = new \ReflectionClass($composerConfig);
        $property = $reflection->getProperty('baseDir');

        $path = $property->getValue($composerConfig);

        $localComposerExtra = $this->composer->getPackage()->getExtra();

        if (array_key_exists($key, $localComposerExtra)) {
            $this->cache[$key] = [
                'found' => true,
                'config' => $localComposerExtra[$key],
                'path' => $path,
            ];

            return true;
        }

        return false;
    }

    /**
     * Search config in the global root package.
     */
    private function locateGlobal(string $key): bool
    {
        $path = $this->composer->getConfig()->get('home');

        $globalComposerJsonFile = $path . '/composer.json';

        if (file_exists($globalComposerJsonFile)) {
            $globalComposerJson = file_get_contents($globalComposerJsonFile);

            if (!$globalComposerJson) {
                throw new \RuntimeException('Could not read global composer.json file');
            }

            $globalComposerConfig = json_decode($globalComposerJson, true, 512, JSON_THROW_ON_ERROR);

            if (array_key_exists('extra', $globalComposerConfig) && array_key_exists($key, $globalComposerConfig['extra'])) {
                $this->cache[$key] = [
                    'found' => true,
                    'config' => $globalComposerConfig['extra'][$key],
                    'path' => $path,
                ];

                return true;
            }
        }

        return false;
    }
}
