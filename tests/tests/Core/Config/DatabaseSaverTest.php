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

    public function testSave()
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

    public function testSaveNonUnique()
    {
        $group1 = $value1 = md5(time() . uniqid());
        $group2 = $value2 = md5(time() . uniqid());
        $item = 'this.is.the.test.key';

        $this->saver->save($item, $value1, 'testing', $group1);
        $this->saver->save($item, $value2, 'testing', $group2);

        $db = Database::getActiveConnection();
        $result = $db->executeQuery(
            'SELECT configValue FROM Config WHERE configItem=? AND configGroup=?',
            array($item, $group1));

        $array = (array)$result->fetch();
        $saved_value = array_shift($array);

        $this->assertEquals($value1, $saved_value, "Failed to save.");

        $result = $db->executeQuery(
            'SELECT configValue FROM Config WHERE configItem=? AND configGroup=?',
            array($item, $group2));

        $array = (array)$result->fetch();
        $saved_value = array_shift($array);

        $this->assertEquals($value2, $saved_value, "Failed to save.");
    }

    public function testSavingArray() {
        $array = array(
            'test' => true,
            'test2' => true
        );

        $this->saver->save('testing', $array, '', 'test');

        $db = Database::getActiveConnection();
        $result = $db->executeQuery(
            'SELECT configValue FROM Config WHERE configItem=? AND configGroup=?',
            array('testing.test', 'test'));

        $array = (array)$result->fetch();
        $saved_value1 = array_shift($array);

        $result = $db->executeQuery(
            'SELECT configValue FROM Config WHERE configItem=? AND configGroup=?',
            array('testing.test', 'test'));

        $array = (array)$result->fetch();
        $saved_value2 = array_shift($array);


        $this->assertTrue($saved_value1 === $saved_value2 && !!$saved_value1 == true, 'Failed to save array.');
    }

    public function testSavingArrayOverArray()
    {
        $group = md5(time() . uniqid());

        $this->saver->save('test.array', array(1, 2), 'testing', $group);
        $this->saver->save('test.array', array(1), 'testing', $group);

        $db = Database::getActiveConnection();
        $result = $db->executeQuery(
            'SELECT configValue FROM Config WHERE configItem LIKE ? AND configGroup=?',
            array('test.array%', $group));

        $array = array_map(function($item) {
            return $item['configValue'];
        }, (array)$result->fetchAll());

        $this->assertEquals(array(1), $array, "Saver doesn't save correctly");
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
