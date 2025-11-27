<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\resources;

use Composer\DependencyResolver\Operation\OperationInterface;
use Pyrech\ComposerChangelogs\Model\Version;
use Pyrech\ComposerChangelogs\OperationHandler\OperationHandler;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class FakeHandler implements OperationHandler
{
    private bool $supports;

    private string $sourceUrl;

    private string $output;

    public function __construct(bool $supports, string $sourceUrl, string $output)
    {
        $this->supports = $supports;
        $this->sourceUrl = $sourceUrl;
        $this->output = $output;
    }

    public function supports(OperationInterface $operation): bool
    {
        return $this->supports;
    }

    public function extractSourceUrl(OperationInterface $operation): ?string
    {
        return $this->sourceUrl;
    }

    public function getOutput(OperationInterface $operation, ?UrlGenerator $urlGenerator = null): array
    {
        if (!($operation instanceof FakeOperation)) {
            return [];
        }

        $output = [
            ' - ' . $this->output . ', ' . $operation->getText(),
        ];

        if ($urlGenerator) {
            $output[] = '   ' . $urlGenerator->generateCompareUrl($this->sourceUrl, new Version('', '', ''), $this->sourceUrl, new Version('', '', ''));
            $output[] = '   ' . $urlGenerator->generateReleaseUrl($this->sourceUrl, new Version('', '', ''));
        }

        return $output;
    }
}
