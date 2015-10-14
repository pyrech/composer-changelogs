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

use Composer\DependencyResolver\Operation\OperationInterface;
use Pyrech\ComposerChangelogs\OperationHandler\OperationHandler;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class Outputter
{
    /** @var OperationHandler[] */
    private $operationHandlers;

    /** @var UrlGenerator[] */
    private $urlGenerators;

    /** @var OperationInterface[] */
    private $operations;

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

    /**
     * @param OperationInterface $operation
     */
    public function addOperation(OperationInterface $operation)
    {
        $this->operations[] = $operation;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        $output = [];

        if (empty($this->operations)) {
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
     * @param array              $output
     * @param OperationInterface $operation
     *
     * @return array|void
     */
    private function createOperationOutput(array &$output, OperationInterface $operation)
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

    /**
     * @param OperationInterface $operation
     *
     * @return OperationHandler|null
     */
    private function getOperationHandler(OperationInterface $operation)
    {
        foreach ($this->operationHandlers as $operationHandler) {
            if ($operationHandler->supports($operation)) {
                return $operationHandler;
            }
        }

        return;
    }

    /**
     * @param string $sourceUrl
     *
     * @return UrlGenerator|null
     */
    private function getUrlGenerator($sourceUrl)
    {
        foreach ($this->urlGenerators as $urlGenerator) {
            if ($urlGenerator->supports($sourceUrl)) {
                return $urlGenerator;
            }
        }

        return;
    }
}
