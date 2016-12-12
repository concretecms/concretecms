<?php
namespace Concrete\Core\File\StorageLocation\Configuration;

class DefaultConfiguration extends LocalConfiguration
{

    protected $default = true;

    public function __construct()
    {
        $this->setRootPath(DIR_FILES_UPLOADED_STANDARD);
        $this->setWebRootRelativePath(REL_DIR_FILES_UPLOADED_STANDARD);
    }

    public function __wakeup()
    {
        $this->setRootPath(DIR_FILES_UPLOADED_STANDARD);
        $this->setWebRootRelativePath(REL_DIR_FILES_UPLOADED_STANDARD);
    }

    public function validateRequest(\Concrete\Core\Http\Request $req)
    {
        return false;
    }

    public function loadFromRequest(\Concrete\Core\Http\Request $req)
    {
        return false;
    }

    public function __sleep()
    {
        return array('default');
    }
}
