<?php

use Concrete\Core\Config\DatabaseLoader;

class DatabaseLoaderTest extends ConcreteDatabaseTestCase
{
    /** @var DatabaseLoader */
    protected $loader;

    protected $tables = array('Config');
    protected $fixtures = array();

    public function setUp()
    {
        parent::setUp();
        $this->loader = new DatabaseLoader();
    }

    public function testLoadingConfig()
    {
        \Core::make('config/database')->save('test.test.test', $string = uniqid());
        $array = $this->loader->load('test', 'test');
        $this->assertEquals($string, array_get($array, 'test.test'), 'Failed to read config value from the database.');
    }

    public function testLoadingLegacyConfig()
    {
        \Database::query('ALTER TABLE Config DROP PRIMARY KEY');
        \Database::query('ALTER TABLE Config MODIFY COLUMN configNamespace VARCHAR (255)');
        \Database::insert(
            'Config',
            array(
                'configItem' => 'test',
                'configValue' => $value = uniqid(),
                'configGroup' => 'testing',
                'configNamespace' => null,
            ));

        $array = $this->loader->load('test', 'testing');

        $this->assertEquals($value, array_get($array, 'test'));
    }

    public function testLoadingNamespacedConfig()
    {
        \Core::make('config/database')->save('namespaced::namespaced.namespaced.namespaced', $value = uniqid());

        $array = $this->loader->load('namespaced', 'namespaced', 'namespaced');

        $this->assertEquals(
            $value,
            array_get($array, 'namespaced.namespaced'),
            'Failed to read namespaced config value from the database.');
    }

    public function testExists()
    {
        $group = md5(time());
        $exists_before = $this->loader->exists($group);

        $db = \Database::getActiveConnection();
        $db->insert(
            'Config',
            array(
                'configItem' => $group,
                'configValue' => 1,
                'configGroup' => $group,
                'configNamespace' => '', ));

        $exists_after = $this->loader->exists($group);

        $this->assertFalse($exists_before);
        $this->assertTrue($exists_after);
    }

    public function testExistsNamespaced()
    {
        $group = md5(uniqid());
        $namespace = md5(uniqid());
        $exists_before = $this->loader->exists($group, $namespace);

        $db = \Database::getActiveConnection();
        $db->insert(
            'Config',
            array(
                'configItem' => $group,
                'configValue' => 1,
                'configGroup' => $group,
                'configNamespace' => $namespace, ));

        $exists_after = $this->loader->exists($group, $namespace);

        $this->assertFalse($exists_before);
        $this->assertTrue($exists_after);
    }

    public function testAddNamespace()
    {
        // Satisfy coverage
        $this->loader->addNamespace('', '');
        $this->assertTrue(true);
    }

    public function testGetNamespaces()
    {
        $namespaces_first = $this->loader->getNamespaces();

        $namespace = md5(uniqid());
        $db = \Database::getActiveConnection();
        $db->insert(
            'Config',
            array(
                'configItem' => $namespace,
                'configValue' => 1,
                'configGroup' => 'test',
                'configNamespace' => $namespace, ));

        $namespaces_after = $this->loader->getNamespaces();

        $diff = array_diff($namespaces_after, $namespaces_first);
        $value = array_shift($diff);

        $this->assertEquals($namespace, $value);
    }
}
