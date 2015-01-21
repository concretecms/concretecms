<?php
namespace Concrete\Core\File;

class EditResponse extends \Concrete\Core\Application\EditResponse
{

    protected $files = array();
    protected $needRefresh = false;
    
    public function setNeedRefresh($needRefresh = true)
    {
        $this->needRefresh = $needRefresh;
    }

    public function setFile(File $file)
    {
        $this->files[] = $file;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function getJSONObject()
    {
        $o = parent::getBaseJSONObject();
        foreach ($this->files as $file) {
            $o->files[] = $file->getJSONObject();
        }
        $o->needRefresh = $this->needRefresh;
        return $o;
    }

}
