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

use Composer\DependencyResolver\Operation\OperationInterface;
use Pyrech\ComposerChangelogs\OperationHandler\OperationHandler;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class Outputter
{
    /** @var OperationHandler[] */
    private array $operationHandlers;

    /** @var UrlGenerator[] */
    private array $urlGenerators;

    /** @var OperationInterface[] */
    private array $operations;

    /**
     * @param OperationHandler[] $operationHandlers
     * @param UrlGenerator[]     $urlGenerators
     */
    public function __construct(array $operationHandlers, array $urlGenerators)
    {
        $this->urlGenerators = $urlGenerators;
        $this->operationHandlers = $operationHandlers;
        $this->operations = [];
    }

    public function addOperation(OperationInterface $operation): void
    {
        $this->operations[] = $operation;
    }

    public function isEmpty(): bool
    {
        return empty($this->operations);
    }

    public function getOutput(): string
    {
        $output = [];

        if ($this->isEmpty()) {
            $output[] = '<fg=green>No changelogs summary</fg=green>';
        } else {
            $output[] = '<fg=green>Changelogs summary:</fg=green>';

            foreach ($this->operations as $operation) {
                $this->createOperationOutput($output, $operation);
            }

            $output[] = '';
        }

        return implode("\n", $output);
    }

    /**
     * @param string[] $output
     */
    private function createOperationOutput(array &$output, OperationInterface $operation): void
    {
        $operationHandler = $this->getOperationHandler($operation);

        if (!$operationHandler) {
            return;
        }

        $output[] = '';

        $urlGenerator = $this->getUrlGenerator(
            $operationHandler->extractSourceUrl($operation)
        );

        $output = array_merge(
            $output,
            $operationHandler->getOutput($operation, $urlGenerator)
        );
    }

    private function getOperationHandler(OperationInterface $operation): ?OperationHandler
    {
        foreach ($this->operationHandlers as $operationHandler) {
            if ($operationHandler->supports($operation)) {
                return $operationHandler;
            }
        }

        return null;
    }

    private function getUrlGenerator(?string $sourceUrl): ?UrlGenerator
    {
        foreach ($this->urlGenerators as $urlGenerator) {
            if ($urlGenerator->supports($sourceUrl)) {
                return $urlGenerator;
            }
        }

        return null;
    }
}
