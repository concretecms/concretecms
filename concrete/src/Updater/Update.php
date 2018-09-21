<?php

namespace Concrete\Core\Updater;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Cache\CacheClearer;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Foundation\Environment\FunctionInspector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Updater\Migrations\Configuration;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Localization;
use Marketplace;

class Update
{
    /**
     * Key of the mutex to be used when performing core upgrades.
     *
     * @var string
     */
    const MUTEX_KEY = 'core_system_upgrade';

    /**
     * Fetch from the remote marketplace the latest available versions of the core and the packages.
     * These operations are done only the first time or after at least APP_VERSION_LATEST_THRESHOLD seconds since the previous check.
     *
     * @return string|null Returns the latest available core version (eg. '5.7.3.1').
     * If we can't retrieve the latest version and if this never succeeded previously, this function returns null
     */
    public static function getLatestAvailableVersionNumber()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        // first, we check session
        $queryWS = false;
        Cache::disableAll();
        $vNum = $config->get('concrete.misc.latest_version', true);
        Cache::enableAll();
        $versionNum = null;
        if (is_object($vNum)) {
            $seconds = strtotime($vNum->timestamp);
            $version = $vNum->value;
            if (is_object($version)) {
                $versionNum = $version->version;
            } else {
                $versionNum = $version;
            }
            $diff = time() - $seconds;
            if ($diff > $config->get('concrete.updates.check_threshold')) {
                // we grab a new value from the service
                $queryWS = true;
            }
        } else {
            $queryWS = true;
        }

        if ($queryWS) {
            $mi = Marketplace::getInstance();
            if ($mi->isConnected()) {
                Marketplace::checkPackageUpdates();
            }
            $update = static::getLatestAvailableUpdate();
            $versionNum = null;
            if (is_object($update)) {
                $versionNum = $update->getVersion();
            }
            if ($versionNum) {
                $config->save('concrete.misc.latest_version', $versionNum);
            } else {
                // we don't know so we're going to assume we're it
                $config->save('concrete.misc.latest_version', APP_VERSION);
            }
        }

        return $versionNum;
    }

    /**
     * Retrieves the info about the latest available information.
     * The effective request to the remote server is done just once per request.
     *
     * @return RemoteApplicationUpdate|null
     */
    public static function getApplicationUpdateInformation()
    {
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache');
        $r = $cache->getItem('APP_UPDATE_INFO');
        if ($r->isMiss()) {
            $r->lock();
            $result = static::getLatestAvailableUpdate();
            $r->set($result)->save();
        } else {
            $result = $r->get();
        }

        return $result;
    }

    /**
     * Looks in the designated updates location for all directories, ascertains what
     * version they represent, and finds all versions greater than the currently installed version of
     * concrete5.
     *
     * @return ApplicationUpdate[]
     */
    public function getLocalAvailableUpdates()
    {
        $app = Application::getFacadeApplication();
        $fh = $app->make('helper/file');
        $updates = [];
        $contents = @$fh->getDirectoryContents(DIR_CORE_UPDATES);
        foreach ($contents as $con) {
            if (is_dir(DIR_CORE_UPDATES . '/' . $con)) {
                $obj = ApplicationUpdate::get($con);
                if (is_object($obj)) {
                    if (version_compare($obj->getUpdateVersion(), APP_VERSION, '>')) {
                        $updates[] = $obj;
                    }
                }
            }
        }
        usort(
            $updates,
            function ($a, $b) {
                return version_compare($a->getUpdateVersion(), $b->getUpdateVersion());
            }
        );

        return $updates;
    }

    /**
     * Checks migrations to see if the current code DB version is greater than that registered in the database.
     */
    public static function isCurrentVersionNewerThanDatabaseVersion()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $config = $app->make('config');
        $database = $db->fetchColumn('select max(version) from SystemDatabaseMigrations');
        $code = $config->get('concrete.version_db');

        return $database < $code;
    }

    /**
     * Upgrade the current core version to the latest locally available by running the applicable migrations.
     *
     * @param null|Configuration $configuration
     *
     * @throws \Concrete\Core\Updater\Migrations\MigrationIncompleteException throws a MigrationIncompleteException exception if there's still some migration pending
     */
    public static function updateToCurrentVersion(Configuration $configuration = null)
    {
        $app = Application::getFacadeApplication();
        $functionInspector = $app->make(FunctionInspector::class);
        if (!$app->isRunThroughCommandLineInterface()) {
            if ($functionInspector->functionAvailable('ignore_user_abort')) {
                // Don't abort the script execution when the web client disconnects,
                // otherwise we risk to halt the execution in the middle of a migration.
                @ignore_user_abort(true);
            }
        }
        $canSetTimeLimit = $functionInspector->functionAvailable('set_time_limit');
        $defaultTimeLimit = null;
        if ($functionInspector->functionAvailable('ini_get')) {
            $defaultTimeLimit = @ini_get('max_execution_time');
            if ($app->make(Numbers::class)->integer($defaultTimeLimit)) {
                $defaultTimeLimit = (int) $defaultTimeLimit;
            } else {
                $defaultTimeLimit = null;
            }
        }
        $config = $app->make('config');
        $clearer = $app->make(CacheClearer::class);
        $clearer->setClearGlobalAreas(false);
        $clearer->flush();

        $em = $app->make(EntityManagerInterface::class);
        $dbm = new DatabaseStructureManager($em);
        $dbm->destroyProxyClasses('ConcreteCore');
        $dbm->generateProxyClasses();

        if (!$configuration) {
            $configuration = new Configuration();
        }

        $configuration->registerPreviousMigratedVersions();
        $isRerunning = $configuration->getForcedInitialMigration() !== null;
        $migrations = $configuration->getMigrationsToExecute('up', $configuration->getLatestVersion());
        $totalMigrations = count($migrations);
        $performedMigrations = 0;
        foreach ($migrations as $migration) {
            if ($defaultTimeLimit !== 0) {
                // The current execution time is not unlimited
                $timeLimitSet = $canSetTimeLimit ? @set_time_limit(max((int) $defaultTimeLimit, 300)) : false;
                if (!$timeLimitSet && $performedMigrations > 0) {
                    // We are not able to reset the execution time limit
                    if ($performedMigrations > 10 || $migration instanceof Migrations\LongRunningMigrationInterface) {
                        // If we have done 10 migrations, or if the next migration requires long time
                        throw new Migrations\MigrationIncompleteException($performedMigrations, $totalMigrations - $performedMigrations);
                    }
                }
            }
            if ($isRerunning && $migration->isMigrated()) {
                $migration->markNotMigrated();
                $migrated = false;
                try {
                    $migration->execute('up');
                    $migrated = true;
                } finally {
                    if (!$migrated) {
                        try {
                            $migration->markMigrated();
                        } catch (Exception $x) {
                        }
                    }
                }
            } else {
                $migration->execute('up');
            }
            ++$performedMigrations;
            if ($defaultTimeLimit !== 0 && !$timeLimitSet && $migration instanceof Migrations\LongRunningMigrationInterface) {
                // The current eecution time is not unlimited, we are unable to reset the time limit, and the performed migration took long time
                if ($performedMigrations < $totalMigrations) {
                    throw new Migrations\MigrationIncompleteException($performedMigrations, $totalMigrations - $performedMigrations);
                }
            }
        }
        try {
            $app->make('helper/file')->makeExecutable(DIR_BASE_CORE . '/bin/concrete5', 'all');
        } catch (Exception $x) {
        }
        $config->save('concrete.version_installed', $config->get('concrete.version'));
        $config->save('concrete.version_db_installed', $config->get('concrete.version_db'));
        $textIndexes = $app->make('config')->get('database.text_indexes');
        $app->make(Connection::class)->createTextIndexes($textIndexes);
    }

    /**
     * Retrieves the info about the latest available information.
     *
     * @return RemoteApplicationUpdate|null
     */
    protected static function getLatestAvailableUpdate()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $client = $app->make('http/client')->setUri($config->get('concrete.updates.services.get_available_updates'));
        $client->getRequest()
            ->setMethod('POST')
            ->getPost()
                ->set('LOCALE', Localization::activeLocale())
                ->set('BASE_URL_FU', Application::getApplicationURL())
                ->set('APP_VERSION', APP_VERSION);
        try {
            $response = $client->send();
            $update = RemoteApplicationUpdateFactory::getFromJSON($response->getBody());
        } catch (Exception $x) {
            $update = null;
        }

        return $update;
    }
}
