<?php

/*
 * This file is part of the composer-changelogs project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

defined('CASTOR_USE_CHDIR') || define('CASTOR_USE_CHDIR', true);

use Castor\Attribute\AsTask;

use function Castor\run;

#[AsTask(description: 'Install dependencies')]
function install(): void
{
    run(['composer', 'install']);
}

#[AsTask(description: 'Check coding standards (use --fix to apply changes)')]
function cs(bool $fix = false): void
{
    run(['vendor/bin/php-cs-fixer', 'fix', '--config=.php-cs-fixer.php', '--diff', ...($fix ? [] : ['--dry-run'])]);
}

#[AsTask(description: 'Run static analysis')]
function phpstan(): void
{
    run(['vendor/bin/phpstan', 'analyse']);
}

#[AsTask(description: 'Run the test suite')]
function test(): void
{
    run(['vendor/bin/simple-phpunit']);
}
