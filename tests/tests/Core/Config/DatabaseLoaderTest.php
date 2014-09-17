<?php
use Concrete\Core\Config\DatabaseLoader;

class DatabaseLoaderTest extends ConcreteDatabaseTestCase
{

    /** @var DatabaseLoader */
    protected $loader;

    protected $tables = array('Config');
    protected $fixtures = array('Config');

    public function setUp()
    {
        parent::setUp();
        $this->loader = new DatabaseLoader();
    }

    public function testLoadingConfig()
    {
        $array = $this->loader->load('test', 'test');

        $this->assertEquals('test', array_get($array, 'test.test'), 'Failed to read config value from the database.');
    }

    public function testLoadingNamespacedConfig()
    {
        $array = $this->loader->load('namespaced', 'namespaced', 'namespaced');

        $this->assertEquals(
            'namespaced',
            array_get($array, 'namespaced.namespaced'),
            'Failed to read namespaced config value from the database.');
    }

}
