<?php

namespace Concrete\Core\Filesystem\Icon;

use Illuminate\Filesystem\Filesystem;

abstract class AbstractIconRepository implements IconRepositoryInterface
{

    abstract public function getPath();
    
    abstract public function getBaseUrl();
    
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
        $r = $this->filesystem->files($this->getPath());
        $icons = [];
        asort($icons);
        foreach($r as $icon) {
            $filename = $this->filesystem->basename($icon);
            $url = $this->getBaseUrl() . $filename;
            $icons[] = new Icon($filename, $url);
        }
        return $icons;
    }


}
