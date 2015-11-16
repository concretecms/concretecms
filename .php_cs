<?php

return Symfony\CS\Config\Config::create()
    ->fixers(array(
        // Don't touch class/file name
        '-psr0',
        // Don't vertically align phpdoc tags
        '-phpdoc_params',
        // Allow spaces in concatenation
        '-concat_without_spaces',
        // Allow double-quoted strings, don't force single-quoted strings
        '-single_quote',
        // We often have @package phpDocs, so they shouldn't be stripped out
        '-phpdoc_no_package',
        // Allow 'return null'
        '-empty_return',
        // Don't force an empty line before namespace declaration
        '-single_blank_line_before_namespace',
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude(array('web/concrete/vendor'))
            ->in(__DIR__)
    )
;
