<?php
namespace Concrete\Core\Updater;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Updater\Migrations\Configuration;
use Core;
use Marketplace;
use Config;
use Localization;
use ORM;
use Exception;
use Concrete\Core\Support\Facade\Application;

class Update
{
    /**
     * Fetch from the remote marketplace the latest available versions of the core and the packages.
     * These operations are done only the first time or after at least APP_VERSION_LATEST_THRESHOLD seconds since the previous check.
     *
     * @return string|null Returns the latest available core version (eg. '5.7.3.1').
     * If we can't retrieve the latest version and if this never succeeded previously, this function returns null
     */
    public static function getLatestAvailableVersionNumber()
    {
        // first, we check session
        $queryWS = false;
        Cache::disableAll();
        $vNum = Config::get('concrete.misc.latest_version', true);
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
            if ($diff > Config::get('concrete.updates.check_threshold')) {
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
                Config::save('concrete.misc.latest_version', $versionNum);
            } else {
                // we don't know so we're going to assume we're it
                Config::save('concrete.misc.latest_version', APP_VERSION);
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
        /* @var $cache \Concrete\Core\Cache\Cache */
        $cache = Core::make('cache');
        $r = $cache->getItem('APP_UPDATE_INFO');
        if ($r->isMiss()) {
            $r->lock();
            $r->set(static::getLatestAvailableUpdate());
        }

        return $r->get();
    }

    /**
     * Retrieves the info about the latest available information.
     *
     * @return RemoteApplicationUpdate|null
     */
    protected static function getLatestAvailableUpdate()
    {
        $update = null;
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
        }

        return $update;
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
        $fh = Core::make('helper/file');
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
        $db = \Database::get();
        $database = $db->GetOne('select max(version) from SystemDatabaseMigrations');
        $code = Config::get('concrete.version_db');

        return $database < $code;
    }

    /**
     * Upgrade the current core version to the latest locally available by running the applicable migrations.
     */
    public static function updateToCurrentVersion(Configuration $configuration = null)
    {
        $cms = Core::make('app');
        $cms->clearCaches();

        $em = ORM::entityManager();
        $dbm = new DatabaseStructureManager($em);
        $dbm->destroyProxyClasses('ConcreteCore');
        $dbm->generateProxyClasses();

        if (!$configuration) {
            $configuration = new \Concrete\Core\Updater\Migrations\Configuration();
        }

        $configuration->registerPreviousMigratedVersions();
        $migrations = $configuration->getMigrationsToExecute('up', $configuration->getLatestVersion());
        foreach ($migrations as $migration) {
            $migration->execute('up');
        }
        try {
            $cms->make('helper/file')->makeExecutable(DIR_BASE_CORE.'/bin/concrete5', 'all');
        } catch (\Exception $x) {
        }
        Config::save('concrete.version_installed', Config::get('concrete.version'));
        Config::save('concrete.version_db_installed', Config::get('concrete.version_db'));
    }
}
