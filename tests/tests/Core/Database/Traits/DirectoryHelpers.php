<?php
namespace Concrete\Tests\Core\Database\Traits;

/**
 * @author Markus Liechti <markus@liechti.io>
 */
trait DirectoryHelpers
{
        /**
     * Clean up and shorten absolut folder paths, 
     * so they can be passed with assert methods
     * 
     * Example: 
     * ....\htdocs\concrete5800/packages/test_metadatadriver_yaml\config\yaml
     * will be converted to
     * config/yaml
     * 
     * @param type $folderPath
     * @return string
     */
    private function folderPathCleaner($folderPath)
    {
        //J:\xampp\htdocs\concrete5800/packages/test_metadatadriver_yaml\config\yaml
        $folderPathCleaned = str_replace("\\", "/", $folderPath);

        $linkParts = explode('/',rtrim($folderPathCleaned,'/'));
        $count = count($linkParts);

        $shortenedPath = $linkParts[$count-2].'/'.$linkParts[$count-1];
        return $shortenedPath;
    }
}
