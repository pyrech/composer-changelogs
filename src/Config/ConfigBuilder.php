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

use Pyrech\ComposerChangelogs\Util\FileSystemHelper;

class ConfigBuilder
{
    private static $validCommitAutoValues = [
        'never',
        'ask',
        'always',
    ];

    /** @var string[] */
    private $warnings = [];

    /**
     * @param array  $extra
     * @param string $baseDir
     *
     * @return Config
     */
    public function build(array $extra, $baseDir)
    {
        $this->reset();

        $commitAuto = 'never';
        $commitBinFile = null;
        $commitMessage = 'Update dependencies';
        $gitlabHosts = [];
        $postUpdatePriority = -1;

        if (array_key_exists('commit-auto', $extra)) {
            if (in_array($extra['commit-auto'], self::$validCommitAutoValues, true)) {
                $commitAuto = $extra['commit-auto'];
            } else {
                $this->warnings[] = self::createWarningFromInvalidValue(
                    $extra,
                    'commit-auto',
                    $commitAuto,
                    sprintf('Valid options are "%s".', implode('", "', self::$validCommitAutoValues))
                );
            }
        }

        if (array_key_exists('commit-bin-file', $extra)) {
            if ('never' === $commitAuto) {
                $this->warnings[] = '"commit-bin-file" is specified but "commit-auto" option is set to "never". Ignoring.';
            } else {
                $file = realpath(
                    FileSystemHelper::isAbsolute($extra['commit-bin-file'])
                    ? $extra['commit-bin-file']
                    : $baseDir . '/' . $extra['commit-bin-file']
                );

                if (!file_exists($file)) {
                    $this->warnings[] = 'The file pointed by the option "commit-bin-file" was not found. Ignoring.';
                } else {
                    $commitBinFile = $file;
                }
            }
        } else {
            if ('never' !== $commitAuto) {
                $this->warnings[] = sprintf(
                    '"commit-auto" is set to "%s" but "commit-bin-file" was not specified.',
                    $commitAuto
                );
            }
        }

        if (array_key_exists('commit-message', $extra)) {
            if (0 === strlen(trim($extra['commit-message']))) {
                $this->warnings[] = '"commit-message" is specified but empty. Ignoring and using default commit message.';
            } else {
                $commitMessage = $extra['commit-message'];
            }
        }

        if (array_key_exists('gitlab-hosts', $extra)) {
            if (!is_array($extra['gitlab-hosts'])) {
                $this->warnings[] = '"gitlab-hosts" is specified but should be an array. Ignoring.';
            } else {
                $gitlabHosts = (array) $extra['gitlab-hosts'];
            }
        }

        if (array_key_exists('post-update-priority', $extra)) {
            if (!preg_match('/^-?\d+$/', $extra['post-update-priority'])) {
                $this->warnings[] = '"post-update-priority" is specified but not an integer. Ignoring and using default commit event priority.';
            } else {
                $postUpdatePriority = (int) $extra['post-update-priority'];
            }
        }

        return new Config($commitAuto, $commitBinFile, $commitMessage, $gitlabHosts, $postUpdatePriority);
    }

    /**
     * @return string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    private function reset()
    {
        $this->warnings = [];
    }

    /**
     * @param array  $extra
     * @param string $key
     * @param mixed  $default
     * @param string $additionalMessage
     *
     * @return string
     */
    private static function createWarningFromInvalidValue(array $extra, $key, $default, $additionalMessage = '')
    {
        $warning = sprintf(
            'Invalid value "%s" for option "%s", defaulting to "%s".',
            $extra[$key],
            $key,
            $default
        );

        if ($additionalMessage) {
            $warning .= ' ' . $additionalMessage;
        }

        return $warning;
    }
}
