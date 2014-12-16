<?php

namespace Concrete\Core\Multilingual\Service;

defined('C5_EXECUTE') or die("Access Denied.");

class Extractor
{
    public function extractTranslatableSiteStrings()
    {
        // first, we figure out which files we're going to operate on.
        $directories = array(
            DIR_APPLICATION . '/' . DIRNAME_BLOCKS,
            DIR_APPLICATION . '/' . DIRNAME_ELEMENTS,
            DIR_APPLICATION . '/' . DIRNAME_CONTROLLERS,
            DIR_APPLICATION . '/' . DIRNAME_MAIL_TEMPLATES,
            DIR_APPLICATION . '/' . DIRNAME_PAGE_TYPES,
            DIR_APPLICATION . '/' . DIRNAME_PAGES,
            DIR_APPLICATION . '/' . DIRNAME_THEMES,
            DIR_APPLICATION . '/' . DIRNAME_VIEWS
        );

        $files = array();
        foreach($directories as $directory) {
            $directoryIterator = new \RecursiveDirectoryIterator($directory);
            $iterator = new \RecursiveIteratorIterator($directoryIterator);
            $results = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
            foreach($results as $result) {
                $files[] = $result[0];
            }
        }

        

    }
}
