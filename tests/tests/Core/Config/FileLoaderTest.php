<?php

use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\Renderer;
use Illuminate\Filesystem\Filesystem;

class FileLoaderTest extends PHPUnit_Framework_TestCase
{

    /** @var FileLoader */
    protected $loader;

    /** @var Filesystem */
    protected $files;

    /** @var string */
    protected $group;

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $environment;

    /** @var array */
    protected $to_remove = array();

    public function setUp()
    {
        $this->loader = new FileLoader($this->files = new Filesystem());
        $this->group = md5(time() . uniqid());
        $this->namespace = md5(time() . uniqid());
        $this->environment = md5(time() . uniqid());

        $path = DIR_APPLICATION . '/config/';
        $this->loader->addNamespace($this->namespace, $path . $this->namespace);

        $paths = array(

            // Environment Override
            "{$this->environment}.{$this->group}.php"                    => array(
                'environment' => true),
            // Main Config
            "{$this->group}.php"                                         => array(
                'main'        => true,
                'environment' => false,
                'namespace' => false),
            // Generated Override
            "generated_overrides/{$this->group}.php"                     => array(
                'generated'   => true,
                'environment' => false,
                'main'        => false,
                'namespace' => false),
            // Namespaced Environment Override
            "{$this->namespace}/{$this->environment}.{$this->group}.php" => array(
                'namespace' => true),
            // Namespaced Main Config
            "{$this->namespace}/{$this->group}.php"                      => array(
                'main'                   => true,
                'environment'            => false,
                'namespace' => false),
            // Namespaced Generated Override
            "generated_overrides/{$this->namespace}/{$this->group}.php"  => array(
                'generated'              => true,
                'environment'            => false,
                'main'                   => false,
                'namespace' => false)
        );

        foreach ($paths as $relative_path => $array) {
            $split = explode('/', $relative_path);
            $current_path = $path;
            array_pop($split);

            foreach ($split as $directory) {
                $dir = "{$current_path}/{$directory}";
                if (!$this->files->exists($dir)) {
                    $this->files->makeDirectory($dir);
                    $this->to_remove[] = $dir;
                }
                $current_path = $dir;
            }
            $this->to_remove[] = $path . $relative_path;

            $this->files->put($path . $relative_path, id(new Renderer($array))->render());
        }
    }

    public function tearDown()
    {
        $remove = array_reverse($this->to_remove);
        foreach ($remove as $path) {
            if ($this->files->isDirectory($path)) {
                $this->files->deleteDirectory($path);
            } else {
                $this->files->delete($path);
            }
        }
    }

    public function testHierarchy()
    {
        $loaded = $this->loader->load($this->environment, $this->group);

        $this->assertTrue(array_get($loaded, 'main'), '"Main" loading out of order.');
        $this->assertTrue(array_get($loaded, 'generated'), '"Generated" loading out of order.');
        $this->assertTrue(array_get($loaded, 'environment'), '"Environment" loading out of order.');
        $this->assertFalse(array_get($loaded, 'namespace'), 'Namespaced "Environment" loading out of order.');
    }

    public function testNamespaceHierarchy()
    {
        $loaded = $this->loader->load($this->environment, $this->group, $this->namespace);

        $this->assertTrue(array_get($loaded, 'main'), '"Main" loading out of order.');
        $this->assertTrue(array_get($loaded, 'generated'), '"Generated" loading out of order.');
        $this->assertTrue(array_get($loaded, 'environment'), '"Environment" loading out of order.');
        $this->assertTrue(array_get($loaded, 'namespace'), 'Namespaced "Environment" loading out of order.');

    }

}
