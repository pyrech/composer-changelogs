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
    /** @var Composer */
    private $composer;

    /** @var array */
    public $cache = [];

    /**
     * @param Composer $composer
     */
    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getConfig($key)
    {
        $this->locate($key);

        return $this->cache[$key]['config'];
    }

    /**
     * @param string $key
     *
     * @return string|null mixed
     */
    public function getPath($key)
    {
        $this->locate($key);

        return $this->cache[$key]['path'];
    }

    /**
     * Try to locate where is the config for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function locate($key)
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
     *
     * @param string $key
     *
     * @return bool
     */
    private function locateLocal($key)
    {
        $composerConfig = $this->composer->getConfig();

        // Sorry for this, I couldn't find any way to get the path of the current root package
        $reflection = new \ReflectionClass($composerConfig);
        $property = $reflection->getProperty('baseDir');
        $property->setAccessible(true);

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
     *
     * @param string $key
     *
     * @return bool
     */
    private function locateGlobal($key)
    {
        $path = $this->composer->getConfig()->get('home');

        $globalComposerJsonFile = $path . '/composer.json';

        if (file_exists($globalComposerJsonFile)) {
            $globalComposerJson = json_decode(file_get_contents($globalComposerJsonFile), true);

            if (array_key_exists('extra', $globalComposerJson) && array_key_exists($key, $globalComposerJson['extra'])) {
                $this->cache[$key] = [
                    'found' => true,
                    'config' => $globalComposerJson['extra'][$key],
                    'path' => $path,
                ];

                return true;
            }
        }

        return false;
    }
}
