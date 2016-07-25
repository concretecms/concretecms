<?php
namespace Concrete\Core\File;

use Concrete\Core\Entity\File\File as FileEntity;

class EditResponse extends \Concrete\Core\Application\EditResponse
{
    protected $files = array();

    public function setFile(FileEntity $file)
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
