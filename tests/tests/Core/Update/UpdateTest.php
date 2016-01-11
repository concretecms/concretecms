<?php

class UpdateTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array('Blocks', 'BlockTypes', 'CollectionVersionBlocks', 'Files', 'Logs', 'SystemDatabaseMigrations', 'Widgets');

    public function testCurrentMigration()
    {
        $directory = dirname(__FILE__) . '/fixtures/';
        $configuration = new \Concrete\Core\Updater\Migrations\Configuration(false);
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);

        $version = $configuration->getCurrentVersion();
        $this->assertEquals('0', $version);

        $version = $configuration->getVersion('20140908071333');
        $this->assertInstanceOf('\Doctrine\DBAL\Migrations\Version', $version);
        $version->markMigrated();

        $version = $configuration->getCurrentVersion();
        $this->assertNotEquals('0', $version);
        $this->assertEquals('20140908071333', $version);
        $version = $configuration->getVersion($version);
        $this->assertInstanceOf('\Doctrine\DBAL\Migrations\Version', $version);
        $this->assertEquals('20140908071333', $version->getVersion());
    }

    public function testUpdate()
    {
        $db = Database::get();
        $this->assertTrue($db->tableExists('Files'));
        $sm = $db->getSchemaManager();

        $directory = dirname(__FILE__) . '/fixtures/';
        $configuration = new \Concrete\Core\Updater\Migrations\Configuration(false);
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);
        $migrations = $configuration->getMigrations();

        $originalLogs = $sm->listTableDetails('Logs');

        $this->assertEquals(2, count($migrations));
        $this->assertTrue(array_key_exists('20140908071333', $migrations));
        $this->assertInstanceOf('\Doctrine\DBAL\Migrations\Version', $migrations['20140908071333']);

        $migrations = $configuration->getMigrationsToExecute('up', '20140908095447');
        $this->assertEquals(2, count($migrations));

        $migrations = $configuration->getMigrationsToExecute('up', '20140908071333');
        $this->assertEquals(1, count($migrations));

        $migration = $migrations['20140908071333'];
        $migration->execute('up');

        $newLogs = $sm->listTableDetails('Logs');
        $fPassword = $sm->listTableDetails('Files')->getColumn('fPassword');

        $this->assertFalse($originalLogs->hasColumn('testcolumn'));
        $this->assertTrue($newLogs->hasColumn('testcolumn'));
        $this->assertInstanceof('\Doctrine\DBAL\Types\TextType', $fPassword->getType());

        $migration->execute('down');

        $fPassword = $sm->listTableDetails('Files')->getColumn('fPassword');
        $newLogs = $sm->listTableDetails('Logs');
        $this->assertInstanceof('\Doctrine\DBAL\Types\StringType', $fPassword->getType());
        $this->assertFalse($newLogs->hasColumn('testcolumn'));

        $migrations = $configuration->getMigrationsToExecute('up', '20140908095447');
        foreach ($migrations as $migration) {
            $migration->execute('up');
        }

        $this->assertTrue($db->tableExists('Widgets'));
        $bt = BlockType::getByHandle('file');
        $this->assertInstanceOf('\Concrete\Core\Block\BlockType\BlockType', $bt);
        $this->assertEquals(2, $bt->getBlockTypeID()); // because we cleared it out once already.

        $ids = $db->GetOne('select count(btID) from BlockTypes');
        $this->assertEquals(1, $ids);
    }
}
