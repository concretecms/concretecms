<?php
namespace Concrete\Core\File;

class EditResponse extends \Concrete\Core\Application\EditResponse
{

    protected $files = array();

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
        return $o;
    }

}
