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

use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class Changelogs
{
    /** @var UrlGenerator[] */
    private $urlGenerators;

    /** @var Update[] */
    private $updates = [];

    /**
     * @param UrlGenerator[] $urlGenerators
     */
    public function __construct(array $urlGenerators)
    {
        $this->urlGenerators = $urlGenerators;
    }

    /**
     * @param Update $update
     */
    public function addUpdate(Update $update)
    {
        $this->updates[] = $update;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->updates);
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        $output = [];
        $output[] = '<fg=green>Changelogs summary</fg=green>';

        foreach ($this->updates as $update) {
            $this->createUpdateOutput($output, $update);
        }

        return implode("\n", $output);
    }

    private function createUpdateOutput(array &$output, Update $update)
    {
        $output[] = '';
        $output[] = sprintf(
            ' - <fg=green>%s</fg=green> updated from <fg=yellow>%s</fg=yellow> to <fg=yellow>%s</fg=yellow>',
            $update->getPackageName(),
            $update->getVersionFrom(),
            $update->getVersionTo()
        );

        $urlGenerator = $this->getUrlGenerator($update);

        if ($urlGenerator) {
            $compareUrl = $urlGenerator->generateCompareUrl($update);

            if ($compareUrl) {
                $output[] = sprintf(
                    '   See changes: %s',
                    $compareUrl
                );
            }

            $releaseUrl = $urlGenerator->generateReleaseUrl($update);

            if ($releaseUrl) {
                $output[] = sprintf(
                    '   Release notes: %s',
                    $releaseUrl
                );
            }
        }
    }

    /**
     * @param Update $update
     *
     * @return UrlGenerator|null
     */
    private function getUrlGenerator(Update $update)
    {
        foreach ($this->urlGenerators as $urlGenerator) {
            if ($urlGenerator->supports($update->getSourceUrl())) {
                return $urlGenerator;
            }
        }

        return;
    }
}
