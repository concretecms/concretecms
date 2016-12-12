<?php

/**
 * Translations for the Zend translator for testing purposes
 *
 * It is easier to maintain the PHP format translations for the test purpsoes.
 * We are not testing the Zend internal components here, only the integration
 * between the Zend library and concrete5 because of which the translation
 * definition format is irrelevant for these tests.
 *
 * For the plural forms arrays:
 * - 0: Singular format
 * - 1: Plural format
 */

$cs = "\x04"; // Context Separator

return array(
    "Hello Translator!" => "A B!",
    "Hello %s!" => "A %s!",
    "Yellow Cat" => array(
        0 => 'X Y',
        1 => 'X Ys',
    ),
    "One Yellow Cat" => array(
        0 => 'D X Y',
        1 => '%d X Ys',
    ),
    "%d Yellow %s" => array(
        0 => '%d X %s',
        1 => '%d X %s',
    ),
    "context${cs}Welcome!" => 'E!',
    "context${cs}Welcome %s!" => 'E %s!',
);
