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
use Pyrech\ComposerChangelogs\Config\ConfigLocator;

class ConfigLocatorTest extends \PHPUnit_Framework_TestCase
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
    protected function setUp()
    {
        $this->localConfigPath = realpath(__DIR__ . '/../fixtures/local');
        $this->globalConfigPath = realpath(__DIR__ . '/../fixtures/home');

        $this->config = new Config(false, $this->localConfigPath);
        $this->config->merge(array(
            'config' => array(
                'home' => $this->globalConfigPath,
            ),
        ));

        $package = new RootPackage('my/project', '1.0.0', '1.0.0');
        $package->setExtra(array(
            'my-local-config' => array(
                'foo' => 'bar',
            ),
        ));

        $this->composer = new Composer();
        $this->composer->setConfig($this->config);
        $this->composer->setPackage($package);

        $this->SUT = new ConfigLocator($this->composer);
    }

    public function test_it_locates_local_config()
    {
        $key = 'my-local-config';

        static::assertTrue($this->SUT->locate($key));

        static::assertSame($this->localConfigPath, $this->SUT->getPath($key));
        static::assertSame(array('foo' => 'bar'), $this->SUT->getConfig($key));
    }

    public function test_it_locates_global_config()
    {
        $key = 'my-global-config';

        static::assertTrue($this->SUT->locate($key));

        static::assertSame($this->globalConfigPath, $this->SUT->getPath($key));
        static::assertSame(array('bar' => 'foo'), $this->SUT->getConfig($key));
    }

    public function test_it_does_not_locate_non_existing_config()
    {
        $key = 'my-non-existing-config';

        static::assertFalse($this->SUT->locate($key));

        static::assertNull($this->SUT->getPath($key));
        static::assertSame(array(), $this->SUT->getConfig($key));
    }
}
