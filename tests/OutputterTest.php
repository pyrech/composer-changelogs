<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests;

use PHPUnit\Framework\TestCase;
use Pyrech\ComposerChangelogs\OperationHandler\OperationHandler;
use Pyrech\ComposerChangelogs\Outputter;
use Pyrech\ComposerChangelogs\tests\resources\FakeHandler;
use Pyrech\ComposerChangelogs\tests\resources\FakeOperation;
use Pyrech\ComposerChangelogs\tests\resources\FakeUrlGenerator;
use Pyrech\ComposerChangelogs\UrlGenerator\UrlGenerator;

class OutputterTest extends TestCase
{
    /** @var Outputter */
    private $SUT;

    /** @var OperationHandler[] */
    private $operationHandlers;

    /** @var UrlGenerator[] */
    private $urlGenerators;

    protected function setUp(): void
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

    public function testItAddsOperation()
    {
        $operation1 = new FakeOperation('');
        $this->SUT->addOperation($operation1);

        $operation2 = new FakeOperation('');
        $this->SUT->addOperation($operation2);

        $expectedOutput = <<<TEXT
<fg=green>Changelogs summary:</fg=green>

 - Output handler 2, 
   /compare-url2
   /release-url2

 - Output handler 2, 
   /compare-url2
   /release-url2

TEXT;

        $this->assertFalse($this->SUT->isEmpty());
        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }

    public function testItOutputsWithNoSupportedUrlGenerator()
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

        $this->assertFalse($this->SUT->isEmpty());
        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }

    public function testItOutputsWithNoSupportedOperationHandler()
    {
        $this->SUT = new Outputter([
            new FakeHandler(false, '', ''),
        ], $this->urlGenerators);

        $this->SUT->addOperation(new FakeOperation('operation 1'));
        $this->SUT->addOperation(new FakeOperation('operation 2'));

        $expectedOutput = <<<TEXT
<fg=green>Changelogs summary:</fg=green>

TEXT;

        $this->assertFalse($this->SUT->isEmpty());
        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }

    public function testItOutputsRightText()
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

        $this->assertFalse($this->SUT->isEmpty());
        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }

    public function testItOutputsNothingWithoutOperation()
    {
        $expectedOutput = <<<TEXT
<fg=green>No changelogs summary</fg=green>
TEXT;

        $this->assertTrue($this->SUT->isEmpty());
        $this->assertSame($expectedOutput, $this->SUT->getOutput());
    }
}
