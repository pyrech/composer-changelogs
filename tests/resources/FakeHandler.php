<?php

/**
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\resources;

use Composer\DependencyResolver\Operation\OperationInterface;
use Pyrech\ComposerChangelogs\OperationHandler\OperationHandler;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;
use Pyrech\ComposerChangelogs\Version;

class FakeHandler implements OperationHandler
{
    /** @var bool */
    private $supports;

    /** @var string */
    private $sourceUrl;

    /** @var string */
    private $output;

    /**
     * @param bool   $supports
     * @param string $sourceUrl
     * @param string $output
     */
    public function __construct($supports, $sourceUrl, $output)
    {
        $this->supports = $supports;
        $this->sourceUrl = $sourceUrl;
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OperationInterface $operation)
    {
        return $this->supports;
    }

    /**
     * {@inheritdoc}
     */
    public function extractSourceUrl(OperationInterface $operation)
    {
        return $this->sourceUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(OperationInterface $operation, UrlGenerator $urlGenerator = null)
    {
        if (!($operation instanceof FakeOperation)) {
            return [];
        }

        $output = [
            ' - '.$this->output.', '.$operation->getText(),
        ];

        if ($urlGenerator) {
            $output[] = '   '.$urlGenerator->generateCompareUrl($this->sourceUrl, new Version('', '', ''), new Version('', '', ''));
            $output[] = '   '.$urlGenerator->generateReleaseUrl($this->sourceUrl, new Version('', '', ''));
        }

        return $output;
    }
}
