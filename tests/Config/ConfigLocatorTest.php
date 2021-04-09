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

use Composer\Composer;
use Composer\Config;
use Composer\Package\RootPackage;
use PHPUnit\Framework\TestCase;
use Pyrech\ComposerChangelogs\Config\ConfigLocator;

class ConfigLocatorTest extends TestCase
{
    /** @var string */
    private $localConfigPath;

    /** @var string */
    private $globalConfigPath;

    /** @var Config */
    private $config;

    /** @var Composer */
    private $composer;

    /** @var ConfigLocator */
    private $SUT;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->localConfigPath = realpath(__DIR__ . '/../fixtures/local');
        $this->globalConfigPath = realpath(__DIR__ . '/../fixtures/home');

        $this->config = new Config(false, $this->localConfigPath);
        $this->config->merge([
            'config' => [
                'home' => $this->globalConfigPath,
            ],
        ]);

        $package = new RootPackage('my/project', '1.0.0', '1.0.0');
        $package->setExtra([
            'my-local-config' => [
                'foo' => 'bar',
            ],
        ]);

        $this->composer = new Composer();
        $this->composer->setConfig($this->config);
        $this->composer->setPackage($package);

        $this->SUT = new ConfigLocator($this->composer);
    }

    public function testItLocatesLocalConfig()
    {
        $key = 'my-local-config';

        $this->assertTrue($this->SUT->locate($key));

        $this->assertSame($this->localConfigPath, $this->SUT->getPath($key));
        $this->assertSame(['foo' => 'bar'], $this->SUT->getConfig($key));
    }

    public function testItLocatesGlobalConfig()
    {
        $key = 'my-global-config';

        $this->assertTrue($this->SUT->locate($key));

        $this->assertSame($this->globalConfigPath, $this->SUT->getPath($key));
        $this->assertSame(['bar' => 'foo'], $this->SUT->getConfig($key));
    }

    public function testItDoesNotLocateNonExistingConfig()
    {
        $key = 'my-non-existing-config';

        $this->assertFalse($this->SUT->locate($key));

        $this->assertNull($this->SUT->getPath($key));
        $this->assertSame([], $this->SUT->getConfig($key));
    }
}
