<?php

$fileHeaderComment = <<<COMMENT
This file is part of the systeme.io Test Project.

Copyright (c) 2023.
COMMENT;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('config')
    ->notPath('bin/console')
    ->notPath('public/index.php')
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
		'@Symfony:risky' => true,
		'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'both'],
		'linebreak_after_opening_tag' => true,
		'mb_str_functions' => true,
		'no_php4_constructor' => true,
		'no_unreachable_default_argument_value' => true,
		'no_useless_else' => true,
		'no_useless_return' => true,
		'php_unit_strict' => true,
		'phpdoc_order' => true,
		'strict_comparison' => true,
		'strict_param' => true,
		'blank_line_between_import_groups' => false,
		'declare_strict_types' => true,
    ])
    ->setFinder($finder)
;
