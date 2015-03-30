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

    /**
     * Non-namespaced order:
     *   /concrete/config/group.php
     *   /application/config/generated_overrides/group.php
     *   /application/config/group.php
     *   /application/config/environment.group.php
     *
     * Namespaced order:
     *   /path/to/namespace/group.php
     *   /path/to/namespace/environment.group.php
     *   /application/config/generated_overrides/namespace/group.php
     *   /application/config/namespace/group.php
     *   /application/config/namespace/environment.group.php
     *
     * @param string $environment
     * @param string $group
     * @param null   $namespace
     * @return array
     */
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

        $paths = array();
        if ($namespace === null) {
            // No namespace, let's load up the concrete config first.
            $items = parent::load($environment, $group, 'core');

            $paths = array(
                "{$path}/generated_overrides/{$group}.php",
                "{$path}/{$group}.php",
                "{$path}/{$environment}.{$group}.php");
        } else {
            $paths = array(
                "{$path}/{$group}.php",
                "{$path}/{$environment}.{$group}.php",
                "{$this->defaultPath}/generated_overrides/{$namespace}/{$group}.php",
                "{$this->defaultPath}/{$namespace}/{$group}.php",
                "{$this->defaultPath}/{$namespace}/{$environment}.{$group}.php");
        }

        foreach ($paths as $file) {
            if ($this->files->exists($file)) {
                $items = $this->mergeEnvironment($items, $file);
            }
        }

        return $items;
    }

    protected function getPath($namespace)
    {
        $path = parent::getPath($namespace);
        if (!$path) {
            $path = "{$this->defaultPath}/{$namespace}";
        }
        return $path;
    }

    public function clearNamespace($namespace)
    {
        $path = $this->getPath($namespace);
        if ($path !== $this->getPath(null) && $this->files->isDirectory($namespace)) {
            $this->files->deleteDirectory($path);
        }
    }

}
