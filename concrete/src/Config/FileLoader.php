<?php
namespace Concrete\Core\Config;

use Illuminate\Filesystem\Filesystem;

class FileLoader implements LoaderInterface {

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The default configuration path.
     *
     * @var string
     */
    protected $defaultPath;

    /**
     * All of the named path hints.
     *
     * @var array
     */
    protected $hints = array();

    /**
     * A cache of whether namespaces and groups exists.
     *
     * @var array
     */
    protected $exists = array();

    /**
     * Create a new file configuration loader.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $defaultPath
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        $this->defaultPath = DIR_CONFIG_SITE;
        $this->addNamespace('core', DIR_BASE_CORE . '/config');
    }

    /**
     * Load the given configuration group.
     *
     * @param  string  $environment
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    protected function defaultLoad($environment, $group, $namespace = null)
    {
        $items = array();

        // First we'll get the root configuration path for the environment which is
        // where all of the configuration files live for that namespace, as well
        // as any environment folders with their specific configuration items.
        $path = $this->getPath($namespace);

        if (is_null($path))
        {
            return $items;
        }

        // First we'll get the main configuration file for the groups. Once we have
        // that we can check for any environment specific files, which will get
        // merged on top of the main arrays to make the environments cascade.
        $file = "{$path}/{$group}.php";

        if ($this->files->exists($file))
        {
            $items = $this->getRequire($file);
        }

        // Finally we're ready to check for the environment specific configuration
        // file which will be merged on top of the main arrays so that they get
        // precedence over them if we are currently in an environments setup.
        $file = "{$path}/{$environment}/{$group}.php";

        if ($this->files->exists($file))
        {
            $items = $this->mergeEnvironment($items, $file);
        }

        return $items;
    }

    /**
     * Non-namespaced order:
     *   /concrete/config/group.php
     *   /application/config/generated_overrides/group.php
     *   /application/config/group.php
     *   /application/config/environment.group.php.
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
     *
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
        if ($namespace === null || $namespace == '') {
            // No namespace, let's load up the concrete config first.
            $items = $this->defaultLoad($environment, $group, 'core');

            $paths = array(
                "{$path}/generated_overrides/{$group}.php",
                "{$path}/{$group}.php",
                "{$path}/{$environment}.{$group}.php", );
        } else {
            $paths = array(
                "{$path}/{$group}.php",
                "{$path}/{$environment}.{$group}.php",
                "{$this->defaultPath}/generated_overrides/{$namespace}/{$group}.php",
                "{$this->defaultPath}/{$namespace}/{$group}.php",
                "{$this->defaultPath}/{$namespace}/{$environment}.{$group}.php", );
        }

        foreach ($paths as $file) {
            if ($this->files->exists($file)) {
                $items = $this->mergeEnvironment($items, $file);
            }
        }

        return $items;
    }

    /**
     * Merge the items in the given file into the items.
     *
     * @param  array   $items
     * @param  string  $file
     * @return array
     */
    protected function mergeEnvironment(array $items, $file)
    {
        return array_replace_recursive($items, $this->getRequire($file));
    }

    /**
     * Determine if the given group exists.
     *
     * @param  string  $group
     * @param  string  $namespace
     * @return bool
     */
    public function exists($group, $namespace = null)
    {
        $key = $group.$namespace;

        // We'll first check to see if we have determined if this namespace and
        // group combination have been checked before. If they have, we will
        // just return the cached result so we don't have to hit the disk.
        if (isset($this->exists[$key]))
        {
            return $this->exists[$key];
        }

        $path = $this->getPath($namespace);

        // To check if a group exists, we will simply get the path based on the
        // namespace, and then check to see if this files exists within that
        // namespace. False is returned if no path exists for a namespace.
        if (is_null($path))
        {
            return $this->exists[$key] = false;
        }

        $file = "{$path}/{$group}.php";

        // Finally, we can simply check if this file exists. We will also cache
        // the value in an array so we don't have to go through this process
        // again on subsequent checks for the existing of the config file.
        $exists = $this->files->exists($file);

        return $this->exists[$key] = $exists;
    }

    /**
     * Apply any cascades to an array of package options.
     *
     * @param  string  $env
     * @param  string  $package
     * @param  string  $group
     * @param  array   $items
     * @return array
     */
    public function cascadePackage($env, $package, $group, $items)
    {
        // First we will look for a configuration file in the packages configuration
        // folder. If it exists, we will load it and merge it with these original
        // options so that we will easily "cascade" a package's configurations.
        $file = "packages/{$package}/{$group}.php";

        if ($this->files->exists($path = $this->defaultPath.'/'.$file))
        {
            $items = array_merge(
                $items, $this->getRequire($path)
            );
        }

        // Once we have merged the regular package configuration we need to look for
        // an environment specific configuration file. If one exists, we will get
        // the contents and merge them on top of this array of options we have.
        $path = $this->getPackagePath($env, $package, $group);

        if ($this->files->exists($path))
        {
            $items = array_merge(
                $items, $this->getRequire($path)
            );
        }

        return $items;
    }

    /**
     * Get the package path for an environment and group.
     *
     * @param  string  $env
     * @param  string  $package
     * @param  string  $group
     * @return string
     */
    protected function getPackagePath($env, $package, $group)
    {
        $file = "packages/{$package}/{$env}/{$group}.php";

        return $this->defaultPath.'/'.$file;
    }

    /**
     * Get the configuration path for a namespace.
     *
     * @param  string  $namespace
     * @return string
     */
    protected function getPath($namespace)
    {
        if (is_null($namespace)) {
            return $this->defaultPath;
        }

        if (isset($this->hints[$namespace])) {
            return $this->hints[$namespace];
        }

        return "{$this->defaultPath}/{$namespace}";
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->hints[$namespace] = $hint;
    }

    /**
     * Clear groups in a namespace
     *
     * @param $namespace
     * @return void
     */
    public function clearNamespace($namespace)
    {
        $path = $this->getPath($namespace);
        if ($path !== $this->getPath(null) && $this->files->isDirectory($namespace)) {
            $this->files->deleteDirectory($path);
        }
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->hints;
    }

    /**
     * Get a file's contents by requiring it.
     *
     * @param  string  $path
     * @return mixed
     */
    protected function getRequire($path)
    {
        return $this->files->getRequire($path);
    }

    /**
     * Get the Filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

}
