<?php

/**
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests;

use Pyrech\ComposerChangelogs\OperationHandler\OperationHandler;
use Pyrech\ComposerChangelogs\Outputter;
use Pyrech\ComposerChangelogs\tests\resources\FakeHandler;
use Pyrech\ComposerChangelogs\tests\resources\FakeOperation;
use Pyrech\ComposerChangelogs\tests\resources\FakeUrlGenerator;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class OutputterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Outputter */
    private $SUT;

    /** @var OperationHandler[] */
    private $operationHandlers;

    /** @var UrlGenerator[] */
    private $urlGenerators;

    protected function setUp()
    {
        $this->operationHandlers = [
            new FakeHandler(false, 'http://domain1', 'Output handler 1'),
            new FakeHandler(true, 'http://domain2', 'Output handler 2'),
            new FakeHandler(true, 'http://domain3', 'Output handler 3'),
        ];

        $this->urlGenerators = [
            new FakeUrlGenerator(false, '/compare-url1', '/release-url1'),
            new FakeUrlGenerator(true, '/compare-url2', '/release-url2'),
            new FakeUrlGenerator(true, '/compare-url3', '/release-url3'),
        ];

        $this->SUT = new Outputter($this->operationHandlers, $this->urlGenerators);
    }

    public function test_it_adds_operation()
    {
        $this->assertAttributeSame([], 'operations', $this->SUT);

        $operation1 = new FakeOperation('');
        $this->SUT->addOperation($operation1);

        $this->assertAttributeSame([
            $operation1,
        ], 'operations', $this->SUT);

        $operation2 = new FakeOperation('');
        $this->SUT->addOperation($operation2);

        $this->assertAttributeSame([
            $operation1,
            $operation2,
        ], 'operations', $this->SUT);
    }

    public function test_it_outputs_with_no_supported_url_generator()
    {
        $this->SUT = new Outputter($this->operationHandlers, [
            new FakeUrlGenerator(false, '', ''),
        ]);

        $this->SUT->addOperation(new FakeOperation('operation 1'));
        $this->SUT->addOperation(new FakeOperation('operation 2'));

        $expectedOutput = <<<TEXT
<fg=green>Changelogs summary:</fg=green>

 - Output handler 2, operation 1

 - Output handler 2, operation 2

TEXT;

        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }

    public function test_it_outputs_with_no_supported_operation_handler()
    {
        $this->SUT = new Outputter([
            new FakeHandler(false, '', ''),
        ], $this->urlGenerators);

        $this->SUT->addOperation(new FakeOperation('operation 1'));
        $this->SUT->addOperation(new FakeOperation('operation 2'));

        $expectedOutput = <<<TEXT
<fg=green>Changelogs summary:</fg=green>

TEXT;

        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }

    public function test_it_outputs_right_text()
    {
        $this->SUT->addOperation(new FakeOperation('operation 1'));
        $this->SUT->addOperation(new FakeOperation('operation 2'));

        $expectedOutput = <<<TEXT
<fg=green>Changelogs summary:</fg=green>

 - Output handler 2, operation 1
   /compare-url2
   /release-url2

 - Output handler 2, operation 2
   /compare-url2
   /release-url2

TEXT;

        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }

    public function test_it_outputs_nothing_without_operation()
    {
        $expectedOutput = <<<TEXT
<fg=green>No changelogs summary</fg=green>
TEXT;

        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }
}
