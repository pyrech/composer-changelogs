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

use Pyrech\ComposerChangelogs\Changelogs;
use Pyrech\ComposerChangelogs\tests\UrlGenerator\FakeUrlGenerator;
use Pyrech\ComposerChangelogs\Update;

class ChangelogsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Changelogs */
    private $SUT;

    protected function setUp()
    {
        $this->SUT = new Changelogs([
            new FakeUrlGenerator(false, '/compare-url1', '/release-url1'),
            new FakeUrlGenerator(true, '/compare-url2', '/release-url2'),
            new FakeUrlGenerator(true, '/compare-url3', '/release-url3'),
        ]);
    }

    public function test_it_adds_update()
    {
        $reflection = new \ReflectionClass(get_class($this->SUT));
        $property = $reflection->getProperty('updates');
        $property->setAccessible(true);

        $update1 = new Update('acme/project1', 'v1.0.0', 'v1.0.1', '/path/to/repo1');
        $this->SUT->addUpdate($update1);

        $this->assertSame([
            $update1,
        ], $property->getValue($this->SUT));

        $update2 = new Update('acme/project2', 'v1.0.1', 'v1.0.2', '/path/to/repo2');
        $this->SUT->addUpdate($update2);

        $this->assertSame([
            $update1,
            $update2,
        ], $property->getValue($this->SUT));
    }

    public function test_it_can_be_empty()
    {
        $this->assertTrue($this->SUT->isEmpty());

        $update1 = new Update('acme/project1', 'v1.0.0', 'v1.0.1', '/path/to/repo1');
        $this->SUT->addUpdate($update1);

        $this->assertFalse($this->SUT->isEmpty());
    }

    public function test_it_outputs_changelogs_with_the_right_supported_url_generator()
    {
        $update1 = new Update('acme/project1', 'v1.0.0', 'v1.0.1', '/path/to/repo1');
        $this->SUT->addUpdate($update1);

        $update2 = new Update('acme/project2', 'v1.0.1', 'v1.0.2', '/path/to/repo2');
        $this->SUT->addUpdate($update2);

        $expected = <<<TEXT
<fg=green>Changelogs summary</fg=green>

 - <fg=green>acme/project1</fg=green> updated from <fg=yellow>v1.0.0</fg=yellow> to <fg=yellow>v1.0.1</fg=yellow>
   See changes: /compare-url2
   Release notes: /release-url2

 - <fg=green>acme/project2</fg=green> updated from <fg=yellow>v1.0.1</fg=yellow> to <fg=yellow>v1.0.2</fg=yellow>
   See changes: /compare-url2
   Release notes: /release-url2

TEXT;
        $this->assertSame($expected, $this->SUT->getOutput());
    }

    public function test_it_outputs_changelogs_without_compare_url_if_not_supported()
    {
        $this->SUT = new Changelogs([
            new FakeUrlGenerator(true, false, '/release-url'),
        ]);

        $update = new Update('acme/project', 'v1.0.0', 'v1.0.1', '/path/to/repo');
        $this->SUT->addUpdate($update);

        $expected = <<<TEXT
<fg=green>Changelogs summary</fg=green>

 - <fg=green>acme/project</fg=green> updated from <fg=yellow>v1.0.0</fg=yellow> to <fg=yellow>v1.0.1</fg=yellow>
   Release notes: /release-url

TEXT;
        $this->assertSame($expected, $this->SUT->getOutput());
    }

    public function test_it_outputs_changelogs_without_release_url_if_not_supported()
    {
        $this->SUT = new Changelogs([
            new FakeUrlGenerator(true, '/compare-url', false),
        ]);

        $update = new Update('acme/project', 'v1.0.0', 'v1.0.1', '/path/to/repo');
        $this->SUT->addUpdate($update);

        $expected = <<<TEXT
<fg=green>Changelogs summary</fg=green>

 - <fg=green>acme/project</fg=green> updated from <fg=yellow>v1.0.0</fg=yellow> to <fg=yellow>v1.0.1</fg=yellow>
   See changes: /compare-url

TEXT;
        $this->assertSame($expected, $this->SUT->getOutput());
    }
}
