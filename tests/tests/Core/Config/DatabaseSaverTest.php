<?php

use Concrete\Core\Config\DatabaseSaver;

class DatabaseSaverTest extends ConcreteDatabaseTestCase
{

    /** @var DatabaseSaver */
    protected $saver;

    protected $tables = array('Config');

    public function setUp()
    {
        parent::setUp();
        $this->saver = new DatabaseSaver();
    }

    public function testSavingConfig()
    {
        $group = md5(time() . uniqid());
        $item = 'this.is.the.test.key';
        $value = $group;

        $this->saver->save($item, $value, 'testing', $group);

        $db = Database::getActiveConnection();
        $result = $db->executeQuery(
            'SELECT configValue FROM Config WHERE configItem=? AND configGroup=?',
            array($item, $group));

        $array = (array)$result->fetch();
        $saved_value = array_shift($array);

        $this->assertEquals($value, $saved_value, "Failed to save.");
    }

    public function testSavingNamespacedConfig()
    {
        $group = md5(time() . uniqid());
        $namespace = md5(time() . uniqid());
        $item = 'this.is.the.test.key';
        $value = $group;

        $this->saver->save($item, $value, 'testing', $group, $namespace);

        $db = Database::getActiveConnection();
        $result = $db->executeQuery(
            'SELECT configValue FROM Config WHERE configItem=? AND configGroup=? AND configNamespace=?',
            array($item, $group, $namespace));

        $array = (array)$result->fetch();
        $saved_value = array_shift($array);

        $this->assertEquals($value, $saved_value, "Failed to save namespaced item.");
    }

}
