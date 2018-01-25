<?php

namespace Concrete\Core\Updater\Migrations;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Application;
use Doctrine\DBAL\Migrations\Configuration\Configuration as DoctrineMigrationConfiguration;
use Doctrine\DBAL\Migrations\Version;
use Exception;

class Configuration extends DoctrineMigrationConfiguration
{
    /**
     * Forced initial migration: inclusive.
     *
     * @var string
     */
    const FORCEDMIGRATION_INCLUSIVE = '>=';

    /**
     * Forced initial migration: exclusive.
     *
     * @var string
     */
    const FORCEDMIGRATION_EXCLUSIVE = '>';

    /**
     * Forced initial migration.
     *
     * @var null|\Doctrine\DBAL\Migrations\Version
     */
    protected $forcedInitialMigration = null;

    /**
     * Construct a migration configuration object.
     *
     * @param bool $registerMigrations set to true to load the currently available migrations
     */
    public function __construct($registerMigrations = true)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        parent::__construct($db);
        $directory = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Updater/Migrations/Migrations';
        $this->setName(t('concrete5 Migrations'));
        $this->setMigrationsNamespace('Concrete\Core\Updater\Migrations\Migrations');
        $this->setMigrationsDirectory($directory);
        if ($registerMigrations) {
            $this->registerMigrationsFromDirectory($directory);
        }
        $this->setMigrationsTableName('SystemDatabaseMigrations');
    }

    /**
     * Force the initial migration.
     *
     * @param string $reference A concrete5 version (eg. '8.3.1') or a migration identifier (eg '20171218000000')
     * @param string $criteria One of the FORCEDMIGRATION_... constants
     */
    public function forceInitialMigration($reference, $criteria = self::FORCEDMIGRATION_INCLUSIVE)
    {
        $reference = trim((string) $reference);
        if ($reference === '') {
            throw new Exception(t('Invalid initial migration reference.'));
        }
        if (!in_array($criteria, [static::FORCEDMIGRATION_INCLUSIVE, static::FORCEDMIGRATION_EXCLUSIVE], true)) {
            throw new Exception(t('Invalid initial migration criteria.'));
        }
        $migration = $this->findInitialMigration($reference, $criteria);
        if ($migration === null) {
            throw new Exception(t('Unable to find a migration with identifier %s', $reference));
        }
        $this->forcedInitialMigration = $migration;
    }

    /**
     * Get the forced initial migration (if set).
     *
     * @return \Doctrine\DBAL\Migrations\Version|null
     */
    public function getForcedInitialMigration()
    {
        return $this->forcedInitialMigration;
    }

    /**
     * Reset the forced initial migration.
     */
    public function resetForceInitialMigration()
    {
        $this->forcedInitialMigration = null;
    }

    /**
     * This is a stupid requirement, but basically, we grab the lowest version number in our
     * system database migrations table, and we loop through all migrations in our file system
     * and for any of those LOWER than the lowest one in the table, we can assume they are included
     * in this migration. We then manually insert these rows into the SystemDatabaseMigrations table
     * so Doctrine isn't stupid and attempt to apply them.
     */
    public function registerPreviousMigratedVersions()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        try {
            $minimum = $db->fetchColumn('select min(version) from SystemDatabaseMigrations');
        } catch (Exception $e) {
            return;
        }
        $migrations = $this->getMigrations();
        $keys = array_keys($migrations);

        if ($keys[0] == $minimum) {
            // This is the first migration in concrete5. That means we have already populated this table.
            return;
        } else {
            // We have to populate this table with all the migrations from the very first migration up to
            // the $minMigration
            foreach ($migrations as $key => $migration) {
                if ($key < $minimum) {
                    $migration->markMigrated();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\Configuration\Configuration::getMigrationsToExecute()
     */
    public function getMigrationsToExecute($direction, $to)
    {
        $result = parent::getMigrationsToExecute($direction, $to);
        $forcedInitialMigration = $this->getForcedInitialMigration();
        if ($forcedInitialMigration !== null && $direction === Version::DIRECTION_UP) {
            $forcedMigrations = [];
            $migrations = $this->getMigrations();
            $result = [];
            $add = false;
            foreach ($migrations as $version => $migration) {
                if ($add === false && $migration === $forcedInitialMigration) {
                    $add = true;
                }
                if ($add === true) {
                    $forcedMigrations[$version] = $migration;
                }
            }
            $missingMigrations = array_diff(array_keys($result), array_keys($forcedMigrations));
            if (count($missingMigrations) > 0) {
                throw new Exception(t('The forced migration is later than the one to be executed (%s)', array_pop($missingMigrations)));
            }
            $result = $forcedMigrations;
        }

        return $result;
    }

    /**
     * Get the initial migration given a reference (in form YYYYMMDDhhmmss or as a core version).
     *
     * @param string $reference The migration reference
     * @param string $criteria One of the FORCEDMIGRATION_... constants
     *
     * @throws Exception throws an Exception if $criteria is not valid
     *
     * @return \Doctrine\DBAL\Migrations\Version|null
     */
    protected function findInitialMigration($reference, $criteria)
    {
        return preg_match('/^' . str_repeat('\d', strlen('YYYYMMDDhhmmss')) . '$/', $reference) ?
            $this->findInitialMigrationByIdentifier($reference, $criteria) :
            $this->findInitialMigrationByCoreVersion($reference, $criteria);
    }

    /**
     * Get the initial migration starting from its identifier (in form YYYYMMDDhhmmss).
     *
     * @param string $identifier The migration identifier
     * @param string $criteria One of the FORCEDMIGRATION_... constants
     *
     * @throws Exception throws an Exception if $criteria is not valid
     *
     * @return \Doctrine\DBAL\Migrations\Version|null
     */
    protected function findInitialMigrationByIdentifier($identifier, $criteria)
    {
        switch ($criteria) {
            case static::FORCEDMIGRATION_INCLUSIVE:
                if ($this->hasVersion($identifier)) {
                    $result = $this->getVersion($identifier);
                } else {
                    $result = null;
                }
                break;
            case static::FORCEDMIGRATION_EXCLUSIVE:
                $allIdentifiers = array_keys($this->getMigrations());
                $migrationIndex = array_search($identifier, $allIdentifiers, true);
                if ($migrationIndex === false) {
                    $result = null;
                } elseif ($migrationIndex === count($allIdentifiers) - 1) {
                    $result = null;
                } else {
                    $result = $this->getVersion($allIdentifiers[$migrationIndex + 1]);
                }
                break;
            default:
                throw new Exception(t('Invalid initial migration criteria.'));
        }

        return $result;
    }

    /**
     * Get the initial migration starting from a core version.
     *
     * @param string $coreVersion The core version
     * @param string $criteria One of the FORCEDMIGRATION_... constants
     *
     * @throws Exception throws an Exception if $criteria is not valid
     *
     * @return \Doctrine\DBAL\Migrations\Version|null
     *
     * @example If looking for version 1.4:
     *
     * 20010101000000 v1.1
     * 20020101000000
     * 20030101000000
     * 20040101000000 v1.3  <- if $criteria is FORCEDMIGRATION_INCLUSIVE
     * 20050101000000       <- if $criteria is FORCEDMIGRATION_EXCLUSIVE
     * 20060101000000
     * 20070101000000 v1.5
     */
    protected function findInitialMigrationByCoreVersion($coreVersion, $criteria)
    {
        $coreVersionNormalized = preg_replace('/(\.0+)+$/', '', $coreVersion);
        if (version_compare($coreVersionNormalized, '5.7') < 0) {
            throw new Exception(t('Invalid version specified (%s).', $coreVersion));
        }
        $migrations = $this->getMigrations();
        $migrationIdentifiers = array_keys($migrations);
        $maxMigrationIndex = count($migrationIdentifiers) - 1;
        $result = null;
        foreach ($migrations as $identifier => $migration) {
            $migrationCoreVersionNormalized = preg_replace('/(\.0+)+$/', '', $migration->getMigration()->getDescription());
            if ($migrationCoreVersionNormalized !== '') {
                $cmp = version_compare($migrationCoreVersionNormalized, $coreVersionNormalized);
                if ($cmp <= 0 || $result === null) {
                    switch ($criteria) {
                        case static::FORCEDMIGRATION_INCLUSIVE:
                            $result = $migration;
                            break;
                        case static::FORCEDMIGRATION_EXCLUSIVE:
                            $migrationIndex = array_search($identifier, $migrationIdentifiers, true);
                            $result = $migrationIndex === $maxMigrationIndex ? null : $migrations[$migrationIdentifiers[$migrationIndex + 1]];
                            break;
                        default:
                            throw new Exception(t('Invalid initial migration criteria.'));
                    }
                }
                if ($cmp >= 0) {
                    break;
                }
            }
        }

        return $result;
    }
}
