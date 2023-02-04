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

use Pyrech\ComposerChangelogs\Model\Config;
use Pyrech\ComposerChangelogs\Util\FileSystemHelper;

class ConfigBuilder
{
    private const COMMIT_AUTO_NEVER = 'never';
    private const COMMIT_AUTO_ASK = 'ask';
    private const COMMIT_AUTO_ALWAYS = 'always';

    private const VALID_COMMIT_AUTO_VALUES = [
        self::COMMIT_AUTO_NEVER,
        self::COMMIT_AUTO_ASK,
        self::COMMIT_AUTO_ALWAYS,
    ];

    /** @var string[] */
    private array $warnings = [];

    /**
     * @param array<string, mixed> $extra
     */
    public function build(array $extra, ?string $baseDir): Config
    {
        $this->reset();

        $commitAuto = self::COMMIT_AUTO_NEVER;
        $commitBinFile = null;
        $commitMessage = 'Update dependencies';
        $gitlabHosts = [];
        $postUpdatePriority = -1;

        if (array_key_exists('commit-auto', $extra)) {
            if (in_array($extra['commit-auto'], self::VALID_COMMIT_AUTO_VALUES, true)) {
                $commitAuto = $extra['commit-auto'];
            } else {
                $this->warnings[] = self::createWarningFromInvalidValue(
                    $extra,
                    'commit-auto',
                    $commitAuto,
                    sprintf('Valid options are "%s".', implode('", "', self::VALID_COMMIT_AUTO_VALUES))
                );
            }
        }

        if (array_key_exists('commit-bin-file', $extra)) {
            if (self::COMMIT_AUTO_NEVER === $commitAuto) {
                $this->warnings[] = '"commit-bin-file" is specified but "commit-auto" option is set to "' . self::COMMIT_AUTO_NEVER . '". Ignoring.';
            } else {
                $file = realpath(
                    FileSystemHelper::isAbsolute($extra['commit-bin-file'])
                    ? $extra['commit-bin-file']
                    : $baseDir . '/' . $extra['commit-bin-file']
                );

                if (!$file || !file_exists($file)) {
                    $this->warnings[] = 'The file pointed by the option "commit-bin-file" was not found. Ignoring.';
                } else {
                    $commitBinFile = $file;
                }
            }
        } elseif (self::COMMIT_AUTO_NEVER !== $commitAuto) {
            $this->warnings[] = sprintf(
                '"commit-auto" is set to "%s" but "commit-bin-file" was not specified.',
                $commitAuto
            );
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
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    private function reset(): void
    {
        $this->warnings = [];
    }

    /**
     * @param array<string, mixed> $extra
     * @param mixed                $default
     */
    private static function createWarningFromInvalidValue(
        array $extra,
        string $key,
        $default,
        string $additionalMessage = ''
    ): string {
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
