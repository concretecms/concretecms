<?php

namespace Concrete\Core\Updater;

use Concrete\Core\Cache\Cache;
use Core;
use Marketplace;
use Config;
use Localization;
use ORM;

class Update
{
    /**
     * Fetch from the remote marketplace the latest available versions of the core and the packages.
     * These operations are done only the first time or after at least APP_VERSION_LATEST_THRESHOLD seconds since the previous check.
     *
     * @return string|null Returns the latest available core version (eg. '5.7.3.1').
     * If we can't retrieve the latest version and if this never succeeded previously, this function returns null.
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
            $versionNum = $update->getVersion();

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
     * @return \stdClass Return an \stdClass instance with these properties:
     * <ul>
     * <li>false|string notes</li>
     * <li>false|string url</li>
     * <li>false|string date</li>
     * <li>null|string version</li>
     * </ul>
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
     * @return \stdClass Return an \stdClass instance with these properties:
     * <ul>
     * <li>false|string notes</li>
     * <li>false|string url</li>
     * <li>false|string date</li>
     * <li>null|string version</li>
     * </ul>
     */
    protected static function getLatestAvailableUpdate()
    {
        if (function_exists('curl_init')) {
            $curl_handle = @curl_init();

            // Check to see if there are proxy settings
            if (Config::get('concrete.proxy.host') != null) {
                @curl_setopt($curl_handle, CURLOPT_PROXY, Config::get('concrete.proxy.host'));
                @curl_setopt($curl_handle, CURLOPT_PROXYPORT, Config::get('concrete.proxy.port'));

                // Check if there is a username/password to access the proxy
                if (Config::get('concrete.proxy.user') != null) {
                    @curl_setopt(
                        $curl_handle,
                        CURLOPT_PROXYUSERPWD,
                        Config::get('concrete.proxy.user') . ':' . Config::get('concrete.proxy.password')
                    );
                }
            }

            @curl_setopt($curl_handle, CURLOPT_URL, Config::get('concrete.updates.services.get_available_updates'));
            @curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
            @curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            @curl_setopt($curl_handle, CURLOPT_POST, true);
            @curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, Config::get('app.curl.verifyPeer'));
            $loc = Localization::getInstance();
            @curl_setopt(
                $curl_handle,
                CURLOPT_POSTFIELDS,
                'LOCALE=' . $loc->activeLocale(
                ) . '&BASE_URL_FULL=' . Core::getApplicationURL() . '&APP_VERSION=' . APP_VERSION
            );
            $body = @curl_exec($curl_handle);

            $update = RemoteApplicationUpdateFactory::getFromJSON($body);
            return $update;
        }

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
        $updates = array();
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
     * Upgrade the current core version to the latest locally available by running the applicable migrations.
     */
    public static function updateToCurrentVersion()
    {
        $cms = Core::make('app');
        $cms->clearCaches();

        $em = ORM::entityManager('core');
        $dbm = Core::make('database/structure', array($em));
        $dbm->destroyProxyClasses('ConcreteCore');
        $dbm->generateProxyClasses();

        $configuration = new \Concrete\Core\Updater\Migrations\Configuration();
        $configuration->registerPreviousMigratedVersions();
        $migrations = $configuration->getMigrationsToExecute('up', $configuration->getLatestVersion());
        foreach ($migrations as $migration) {
            $migration->execute('up');
        }
        Config::save('concrete.version_installed', Config::get('concrete.version'));
    }
}
