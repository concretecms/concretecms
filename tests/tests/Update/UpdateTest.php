<?php

namespace Concrete\Tests\Update;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;

class UpdateTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = [];
    protected $tables = ['Blocks', 'CollectionVersionBlocks', 'Logs', 'SystemDatabaseMigrations', 'Widgets'];

    protected $metadatas = [
        BlockType::class,
        File::class,
    ];

    public function testCurrentMigration()
    {
        \Concrete\Core\Block\BlockType\BlockType::installBlockType('core_scrapbook_display');
        $directory = DIR_TESTS . '/assets/Update/';
        $configuration = new \Concrete\Core\Updater\Migrations\Configuration(false);
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);

        $version = $configuration->getCurrentVersion();
        $this->assertEquals('0', $version);

        $version = $configuration->getVersion('20140908071333');
        $this->assertInstanceOf(Version::class, $version);
        $version->markMigrated();

        $version = $configuration->getCurrentVersion();
        $this->assertNotEquals('0', $version);
        $this->assertEquals('20140908071333', $version);
        $version = $configuration->getVersion($version);
        $this->assertInstanceOf(Version::class, $version);
        $this->assertEquals('20140908071333', $version->getVersion());

        $version->markNotMigrated();
    }

    public function testUpdate()
    {
        $db = Application::make(Connection::class);
        $this->assertTrue($db->tableExists('Files'));
        $sm = $db->getSchemaManager();
        $db->exec('truncate BlockTypes');

        $directory = DIR_TESTS . '/assets/Update/';
        $configuration = new \Concrete\Core\Updater\Migrations\Configuration(false);
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);
        $migrations = $configuration->getMigrations();

        $originalLogs = $sm->listTableDetails('Logs');

        $this->assertCount(2, $migrations);
        $this->assertTrue(array_key_exists('20140908071333', $migrations));
        $this->assertInstanceOf(Version::class, $migrations['20140908071333']);

        $migrations = $configuration->getMigrationsToExecute('up', '20140908095447');
        $this->assertCount(2, $migrations);

        $migrations = $configuration->getMigrationsToExecute('up', '20140908071333');
        $this->assertCount(1, $migrations);

        $migration = $migrations['20140908071333'];
        $migration->execute('up');

        $newLogs = $sm->listTableDetails('Logs');
        $fPassword = $sm->listTableDetails('Files')->getColumn('fPassword');

        $this->assertFalse($originalLogs->hasColumn('testcolumn'));
        $this->assertTrue($newLogs->hasColumn('testcolumn'));
        $this->assertInstanceOf(TextType::class, $fPassword->getType());

        $migration->execute('down');

        $fPassword = $sm->listTableDetails('Files')->getColumn('fPassword');
        $newLogs = $sm->listTableDetails('Logs');
        $this->assertInstanceOf(StringType::class, $fPassword->getType());
        $this->assertFalse($newLogs->hasColumn('testcolumn'));

        $migrations = $configuration->getMigrationsToExecute('up', '20140908095447');
        foreach ($migrations as $migration) {
            $migration->execute('up');
        }

        $this->assertTrue($db->tableExists('Widgets'));
        $bt = \Concrete\Core\Block\BlockType\BlockType::getByHandle('file');
        $this->assertInstanceOf(BlockType::class, $bt);
        $this->assertEquals(2, $bt->getBlockTypeID()); // because we cleared it out once already.

        $ids = $db->GetOne('select count(btID) from BlockTypes');
        $this->assertEquals(1, $ids);
    }
}
