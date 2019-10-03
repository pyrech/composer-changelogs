<?php

$header = <<<EOF
This file is part of the composer-changelogs project.

(c) Loïck Piera <pyrech@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;


$finder = \PhpCsFixer\Finder::create()
    ->in(array(__DIR__))
;

return \PhpCsFixer\Config::create()
    // Set to Symfony Level (PSR1 PSR2)
    ->setRules([
        '@Symfony' => true,

        // Add the provided header comment ($header)
        'header_comment' => [
            'header' => $header
        ],

        // Force new line after <?php
        'blank_line_after_opening_tag' => true,

        // Order "use" alphabetically
        'ordered_imports' => true,

        // Replace array() by []
        'array_syntax' => [
            'syntax' => 'short'
        ],

        // Keep return null;
        'simplified_null_return' => false,

        // Clean up the /** php doc */
        'phpdoc_order' => true,

        // Force space around concatenation operator
        'concat_space' => [
            'spacing' => 'one'
        ],

        // Strict comparison
        'strict_comparison' => true,

        // Functions should use $strict param
        'strict_param' => true,

        // Do not convert heredoc to nowdoc
        'heredoc_to_nowdoc' => false
    ])
    ->setUsingCache(true)
    ->setFinder($finder)
    ->setRiskyAllowed(true);
;
