<?php

namespace Concrete\Core\Updater\Migrations;

use RuntimeException;

/**
 * Exception thrown when the migration.
 */
class MigrationIncompleteException extends RuntimeException
{
    /**
     * The number of applied migrations.
     *
     * @var int
     */
    protected $performedMigrations;
    /**
     * The number of remaining migrations.
     *
     * @var int
     */
    protected $remainingMigrations;

    /**
     * @param int $performedMigrations the number of applied migrations
     * @param int $remainingMigrations the number of remaining migrations
     */
    public function __construct($performedMigrations, $remainingMigrations)
    {
        $this->performedMigrations = (int) $performedMigrations;
        $this->remainingMigrations = (int) $remainingMigrations;
        parent::__construct(t(/*i18n: %1$s and %2$s are numbers */'The upgrade process is incomplete (migrations performed: %1$s, migrations remaining: %2$s). Please execute the upgrade process again.'), $this->performedMigrations, $this->remainingMigrations);
    }

    /**
     * Get the number of applied migrations.
     *
     * @return int
     */
    public function getPerformedMigrations()
    {
        return $this->performedMigrations;
    }

    /**
     * Get the number of remaining migrations.
     *
     * @return int
     */
    public function getRemainingMigrations()
    {
        return $this->remainingMigrations;
    }
}
