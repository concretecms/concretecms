<?php
namespace Concrete\Core\Config;

use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;

class ConfigLoader extends FileLoader
{

    public function __construct(Filesystem $files)
    {
        parent::__construct($files, DIR_APPLICATION . '/config');
        $this->addNamespace('core', DIR_BASE . '/concrete/config');
    }

    public function load($environment, $group, $namespace = null)
    {
        /**
         * Custom loading for the concrete group. This file is written to dynamically when configuration values change.
         */
        if (is_null($namespace)) {
            return array_replace_recursive(
                (array)$this->loadStrict('generated_overrides', $group, $namespace),
                array_replace_recursive(
                    (array)$this->loadStrict($environment, $group, 'core'),
                    (array)$this->loadStrict($environment, $group, $namespace)
                )
            );
        }
        return $this->loadStrict($environment, $group, $namespace);
    }

    public function loadStrict($environment, $group, $namespace = null)
    {
        $items = array();

        // First we'll get the root configuration path for the environment which is
        // where all of the configuration files live for that namespace, as well
        // as any environment folders with their specific configuration items.
        $path = $this->getPath($namespace);

        if (is_null($path)) {
            return $items;
        }

        // First we'll get the main configuration file for the groups. Once we have
        // that we can check for any environment specific files, which will get
        // merged on top of the main arrays to make the environments cascade.
        $file = "{$path}/{$group}.php";

        if ($this->files->exists($file)) {
            $items = (array)$this->files->getRequire($file);
        }

        // Finally we're ready to check for the environment specific configuration
        // file which will be merged on top of the main arrays so that they get
        // precedence over them if we are currently in an environments setup.
        $file = "{$path}/{$environment}/{$group}.php";

        if ($this->files->exists($file)) {
            $items = $this->mergeEnvironment($items, $file);
        }

        return $items;
    }
}
