<?php

namespace Concrete\Core\Page\Container;

use Illuminate\Filesystem\Filesystem;

/**
 * Class IconRepository
 * 
 * Responsible for retrieving a list of icon objects representing area containers.
 */
class IconRepository
{
    
    /**
     * @var Filesystem 
     */
    protected $filesystem;
    
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
    
    public function getIcons()
    {
        $path = DIR_BASE_CORE . 
            DIRECTORY_SEPARATOR . 
            DIRNAME_IMAGES . 
            DIRECTORY_SEPARATOR .
            'icons' .
            DIRECTORY_SEPARATOR .
            'containers';
        
        $r = $this->filesystem->files($path);
        $icons = [];
        asort($icons);
        foreach($r as $icon) {
            $filename = $this->filesystem->basename($icon);
            $url = ASSETS_URL_IMAGES . '/icons/containers/' . $filename;
            $icons[] = new Icon($filename, $url);
        }
        return $icons;        
    }

}
