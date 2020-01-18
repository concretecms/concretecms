<?php

namespace Concrete\Core\Filesystem\FileLocator;

/**
 * Class NodeModulesLocation
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
