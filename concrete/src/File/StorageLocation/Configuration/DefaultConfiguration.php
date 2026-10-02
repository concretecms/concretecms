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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration::validateRequest()
     */
    public function validateRequest(\Concrete\Core\Http\Request $req)
    {
        // The paths are fixed: nothing to validate
        return app('error');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration::loadFromRequest()
     */
    public function loadFromRequest(\Concrete\Core\Http\Request $req)
    {
        // The paths are fixed: nothing to load
    }

    public function __sleep()
    {
        return array('default');
    }
}
