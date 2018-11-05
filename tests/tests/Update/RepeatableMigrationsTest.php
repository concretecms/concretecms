<?php

namespace Concrete\Tests\Update;

use Concrete\Core\Updater\Migrations\Configuration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Migrations\Version;

class RepeatableMigrationsTest extends ConcreteDatabaseTestCase
{
    protected $allowedNotRepeatableMigrations = [
        \Concrete\Core\Updater\Migrations\Migrations\Version20160725000000::class,
    ];

    public function testRepeatableMigrations()
    {
        $configuration = new Configuration();
        $versions = $configuration->getMigrations();
        $migrationInstances = array_map(function (Version $version) { return $version->getMigration(); }, $versions);
        $notRepeatableMigrationInstances = array_filter($migrationInstances, function (AbstractMigration $migration) {
            return !$migration instanceof RepeatableMigrationInterface;
        });
        $notRepeatableMigrationClassNames = array_map('get_class', $notRepeatableMigrationInstances);
        $wrongMigrations = array_diff($notRepeatableMigrationClassNames, $this->allowedNotRepeatableMigrations);
        $this->assertEmpty(
            $wrongMigrations,
            sprintf(
                "These migrations should implement the \%s interface (if they are repeatable)\n" .
                "or they should be listed in the \$allowedNotRepeatableMigrations variable of the file %s (if they are not repeatable):\n- ",
                RepeatableMigrationInterface::class,
                substr(__FILE__, strlen(DIR_TESTS) + 1)
            ) . implode("\n- ", $wrongMigrations)
        );
    }
}
