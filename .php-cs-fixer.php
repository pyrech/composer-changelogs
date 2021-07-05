<?php

$header = <<<EOF
This file is part of the composer-changelogs project.

(c) Loïck Piera <pyrech@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__])
;

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],    // Replace array() by []
        'blank_line_after_opening_tag' => true,     // Force new line after <?php
        'concat_space' => ['spacing' => 'one'],     // Force space around concatenation operator
        'header_comment' => ['header' => $header],  // Add the provided header comment ($header)
        'heredoc_to_nowdoc' => false,               // Do not convert heredoc to nowdoc
        'no_superfluous_phpdoc_tags' => false,      // Ensure complete PHPDoc annotations for all params
        'phpdoc_order' => true,                     // Order "use" statements alphabetically
        'simplified_null_return' => false,          // Keep return null;
        'single_line_throw' => false,               // Allow throwing exceptions in more than one row
        'strict_comparison' => true,                // Strict comparison
        'strict_param' => true,                     // Functions should use $strict param
    ])
    ->setUsingCache(true)
    ->setFinder($finder)
;

return $config;
