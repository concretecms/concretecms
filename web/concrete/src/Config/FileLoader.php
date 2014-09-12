<?php
namespace Concrete\Core\Config;

use Illuminate\Filesystem\Filesystem;

class FileLoader extends \Illuminate\Config\FileLoader implements LoaderInterface
{

    public function __construct(Filesystem $files)
    {
        parent::__construct($files, DIR_APPLICATION . '/config');
        $this->addNamespace('core', DIR_BASE_CORE . '/config');
    }

    public function load($environment, $group, $namespace = null)
    {
        $items = array();

        // First we'll get the root configuration path for the environment which is
        // where all of the configuration files live for that namespace, as well
        // as any environment folders with their specific configuration items.
        $path = $this->getPath($namespace);

        if (is_null($path)) {
            return $items;
        }

        if ($namespace === null) {
            // No namespace, let's load up the concrete config first.
            $items = parent::load($environment, $group, 'core');

            $file = "{$path}/generated_overrides/{$group}.php";
            if ($this->files->exists($file)) {
                $items = $this->mergeEnvironment($items, $file);
            }

            $file = "{$path}/{$group}.php";

            if ($this->files->exists($file)) {
                $items = $this->mergeEnvironment($items, $file);
            }
        } else {
            // First we'll get the main configuration file for the groups. Once we have
            // that we can check for any environment specific files, which will get
            // merged on top of the main arrays to make the environments cascade.
            $file = "{$path}/{$group}.php";

            if ($this->files->exists($file)) {
                $items = (array)$this->files->getRequire($file);
            }

            $file = "{$path}/generated_overrides/{$namespace}/{$group}.php";
            if ($this->files->exists($file)) {
                $items = $this->mergeEnvironment($items, $file);
            }

            $file = "{$path}/{$namespace}/{$group}.php";
            if ($this->files->exists($file)) {
                $items = $this->mergeEnvironment($items, $file);
            }
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

    public function clearNamespace($namespace)
    {
        $path = $this->getPath($namespace);
        if ($path !== $this->getPath(null) && $this->files->isDirectory($namespace)) {
            $this->files->deleteDirectory($path);
        }
    }

}
