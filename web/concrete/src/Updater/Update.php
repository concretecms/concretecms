<?php
namespace Concrete\Core\Updater;

use Concrete\Core\Cache\Cache;
use Core;
use Loader;
use Marketplace;
use Config;
use Localization;

class Update
{

    public static function getLatestAvailableVersionNumber()
    {
        $d = Loader::helper('date');
        // first, we check session
        $queryWS = false;
        Cache::disableAll();
        $vNum = Config::get('concrete.misc.latest_version', true);
        Cache::enableAll();
        if (is_object($vNum)) {
            $seconds = strtotime($vNum->timestamp);
            $version = $vNum->value;
            if (is_object($version)) {
                $versionNum = $version->version;
            } else {
                $versionNum = $version;
            }
            $diff = time() - $seconds;
            if ($diff > APP_VERSION_LATEST_THRESHOLD) {
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
            $versionNum = $update->version;

            if ($versionNum) {
                Config::save('concrete.misc.latest_version', $versionNum);
            } else {
                // we don't know so we're going to assume we're it
                Config::save('concrete.misc.latest_version', APP_VERSION);
            }
        }

        return $versionNum;
    }

    public static function getApplicationUpdateInformation()
    {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache');
        $r = $cache->getItem('APP_UPDATE_INFO');
        if ($r->isMiss()) {
            $r->lock();
            $r->set(static::getLatestAvailableUpdate());
        }
        return $r->get();
    }

    protected static function getLatestAvailableUpdate()
    {
        $obj = new \stdClass;
        $obj->notes = false;
        $obj->url = false;
        $obj->date = false;

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

            @curl_setopt($curl_handle, CURLOPT_URL, APP_VERSION_LATEST_WS);
            @curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
            @curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            @curl_setopt($curl_handle, CURLOPT_POST, true);
            $loc = Localization::getInstance();
            @curl_setopt(
                $curl_handle,
                CURLOPT_POSTFIELDS,
                'LOCALE=' . $loc->activeLocale(
                ) . '&BASE_URL_FULL=' . BASE_URL . '/' . DIR_REL . '&APP_VERSION=' . APP_VERSION
            );
            $resp = @curl_exec($curl_handle);

            $xml = @simplexml_load_string($resp);
            if ($xml === false) {
                // invalid. That means it's old and it's just the version
                $obj->version = trim($resp);
            } else {
                $obj = new \stdClass;
                $obj->version = (string)$xml->version;
                $obj->notes = (string)$xml->notes;
                $obj->url = (string)$xml->url;
                $obj->date = (string)$xml->date;
            }

        } else {
            $obj->version = APP_VERSION;
        }

        return $obj;
    }

    /**
     * Looks in the designated updates location for all directories, ascertains what
     * version they represent, and finds all versions greater than the currently installed version of
     * concrete5
     */
    public function getLocalAvailableUpdates()
    {
        $fh = Loader::helper('file');
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

    public static function updateToCurrentVersion()
    {
        $cms = Core::make('app');
        $cms->clearCaches();

        $configuration = new \Concrete\Core\Updater\Migrations\Configuration();
        $configuration->registerPreviousMigratedVersions();
        $migrations = $configuration->getMigrationsToExecute('up', $configuration->getLatestVersion());
        foreach($migrations as $migration) {
            $migration->execute('up');
        }
        Config::save('concrete.version_installed', Config::get('concrete.version'));
    }

}
