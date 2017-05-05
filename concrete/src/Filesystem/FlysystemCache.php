<?php
namespace Concrete\Core\Filesystem;

use League\Flysystem\Cached\CachedAdapter;

class FlysystemCache extends CachedAdapter
{
    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        if ('/' . $directory == REL_DIR_FILES_INCOMING) {
            return $this->getAdapter()->listContents($directory, $recursive);
        }

        return parent::listContents($directory, $recursive);
    }
}
