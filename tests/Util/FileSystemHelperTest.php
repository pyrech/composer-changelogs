<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\ComposerChangelogs\tests\Util;

use PHPUnit\Framework\TestCase;
use Pyrech\ComposerChangelogs\Util\FileSystemHelper;

class FileSystemHelperTest extends TestCase
{
    public function testItCorrectlyDifferentiatesAbsolutePathsFromRelativeOnes()
    {
        $this->assertTrue(FileSystemHelper::isAbsolute('/var/lib'));
        $this->assertTrue(FileSystemHelper::isAbsolute('c:\\\\var\\lib'));
        $this->assertTrue(FileSystemHelper::isAbsolute('\\var\\lib'));

        $this->assertFalse(FileSystemHelper::isAbsolute('var/lib'));
        $this->assertFalse(FileSystemHelper::isAbsolute('../var/lib'));
        $this->assertFalse(FileSystemHelper::isAbsolute(''));
    }
}
