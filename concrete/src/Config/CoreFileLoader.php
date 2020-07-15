<?php

namespace Concrete\Core\Config;

use Illuminate\Filesystem\Filesystem;

/**
 * A file loader specific to default core file loading.
 * This loader only loads the main config file and environment specific config.
 */
class CoreFileLoader extends FileLoader
{

    /**
     * Overridden constructor for the custom `$defaultPath = ...`
     *
     * @param Filesystem $files
     * @param string $defaultPath
     */
    public function __construct(Filesystem $files, $defaultPath = DIR_CORE_CONFIG)
    {
        parent::__construct($files, $defaultPath);
    }

    /**
     * Load files from the core
     *
     * @param string $environment
     * @param string $group
     * @param null $namespace
     * @return array
     */
    public function load($environment, $group, $namespace = null)
    {
        return $this->defaultLoad($environment, $group, $namespace);
    }
}
