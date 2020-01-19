<?php

namespace Concrete\Core\Filesystem\FileLocator;

/**
 * Class NodeModulesLocation.
 *
 * Enables the possibility to load assets from {webroot}/node_modules
 */
class NodeModulesLocation extends AbstractLocation
{
    /**
     * @return string
     */
    public function getCacheKey()
    {
        return 'node_modules';
    }

    public function getPath()
    {
        return DIR_BASE;
    }

    public function getURL()
    {
        return ASSETS_URL_NODE_MODULES;
    }
}
