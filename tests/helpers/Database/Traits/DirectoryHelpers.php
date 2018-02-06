<?php

namespace Concrete\TestHelpers\Database\Traits;

/**
 * @author Markus Liechti <markus@liechti.io>
 */
trait DirectoryHelpers
{
    /**
     * Clean up and shorten absolut folder paths,
     * so they can be passed with assert methods.
     *
     * Example:
     * ....\htdocs\concrete5800/packages/test_metadatadriver_yaml\config\yaml
     * will be converted to
     * config/yaml
     *
     * @param string $folderPath
     * @param int $returnLastFolders
     *
     * @return string
     */
    private function folderPathCleaner($folderPath, $returnLastFolders = 2)
    {
        //...\htdocs\concrete5800/packages/test_metadatadriver_yaml\config\yaml
        $folderPathCleaned = str_replace('\\', '/', $folderPath);

        $linkParts = explode('/', rtrim($folderPathCleaned, '/'));
        $count = count($linkParts);

        $shortenedPath = '';

        for ($i = $returnLastFolders; $i >= 1; --$i) {
            $shortenedPath .= $linkParts[$count - $i] . '/';
        }

        return rtrim($shortenedPath, '/');
    }
}
