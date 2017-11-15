<?php

use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\Renderer;
use Illuminate\Filesystem\Filesystem;

/**
 * Class FileLoaderTest.
 *
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
 */
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
            // No Namespace
            "generated_overrides/{$this->group}.php" => array(
                'non-namespaced' => true,
                'override' => true,
                'second' => false, // This isn't the second one
            ),
            "{$this->group}.php" => array(
                'non-namespaced' => true,
                'main_group' => true,
                'second' => true, // This is the second one, nothing after this should override this value
                'last' => false, // This isn't the last one
            ),
            "{$this->environment}.{$this->group}.php" => array(
                'non-namespaced' => true,
                'environment' => true,
                'last' => true, // This is the last one, nothing should load after this.
            ),
            // Namespace
            "generated_overrides/{$this->namespace}/{$this->group}.php" => array(
                'namespaced' => true,
                'override' => true,
                'second' => false, // This isn't the second one
            ),
            "{$this->namespace}/{$this->group}.php" => array(
                'namespaced' => true,
                'main_group' => true,
                'second' => true, // This is the second one, nothing after this should override this value
                'last' => false, // This isn't the last one
            ),
            "{$this->namespace}/{$this->environment}.{$this->group}.php" => array(
                'namespaced' => true,
                'environment' => true,
                'last' => true, // This is the last one, nothing should load after this.
            ),
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

        $this->assertTrue(array_get($loaded, 'override'), 'Override didn\'t load');
        $this->assertTrue(array_get($loaded, 'main_group'), 'Main group didn\'t load');
        $this->assertTrue(array_get($loaded, 'environment'), 'Environment didn\'t load');
        $this->assertTrue(array_get($loaded, 'second'), 'Second loaded out of order');
        $this->assertTrue(array_get($loaded, 'last'), 'Last loaded out of order');
        $this->assertNull(array_get($loaded, 'namespaced'), 'Loaded a namespaced file... that\'s wrong...');
    }

    public function testNamespaceHierarchy()
    {
        $loaded = $this->loader->load($this->environment, $this->group, $this->namespace);

        $this->assertTrue(array_get($loaded, 'override'), 'Override didn\'t load');
        $this->assertTrue(array_get($loaded, 'main_group'), 'Main group didn\'t load');
        $this->assertTrue(array_get($loaded, 'environment'), 'Environment didn\'t load');
        $this->assertTrue(array_get($loaded, 'second'), 'Second loaded out of order');
        $this->assertTrue(array_get($loaded, 'last'), 'Last loaded out of order');
        $this->assertNull(array_get($loaded, 'non-namespaced'), 'Loaded a namespaced file... that\'s wrong...');
    }
}
