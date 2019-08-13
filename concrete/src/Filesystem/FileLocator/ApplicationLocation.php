<?php
namespace Concrete\Core\Filesystem\FileLocator;

/**
 * @since 8.2.0
 */
class ApplicationLocation extends AbstractLocation
{

    public function getCacheKey()
    {
        return 'application';
    }

    public function getPath()
    {
        return DIR_APPLICATION;
    }

    public function getURL()
    {
        return REL_DIR_APPLICATION;
    }

    public function isOverride()
    {
        return true;
    }

}
