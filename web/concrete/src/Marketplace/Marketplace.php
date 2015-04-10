<?php
namespace Concrete\Core\Marketplace;

use Config;
use Loader;
use Package;
use TaskPermission;
use URL;

class Marketplace
{

    const E_INVALID_BASE_URL = 20;
    const E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED = 21;
    const E_UNRECOGNIZED_SITE_TOKEN = 22;
    const E_DELETED_SITE_TOKEN = 31;
    const E_GENERAL_CONNECTION_ERROR = 99;

    protected $isConnected = false;
    protected $connectionError = false;

    public function __construct()
    {
        if (!Config::get('concrete.marketplace.enabled')) {
            $this->connectionError = Marketplace::E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED;
            return;
        }

        $dbConfig = \Core::make('config/database');
        $csToken = $dbConfig->get('concrete.marketplace.token');
        if ($csToken != '') {

            $fh = Loader::helper('file');
            $csiURL = urlencode(\Core::getApplicationURL());
            $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.connect_validate') . "?csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
            $vn = Loader::helper('validation/numbers');
            $r = $fh->getContents($url);
            if ($r == false) {
                $this->isConnected = true;
            } else {
                if ($vn->integer($r)) {
                    $this->isConnected = false;
                    $this->connectionError = $r;

                    if ($this->connectionError == Marketplace::E_DELETED_SITE_TOKEN) {
                        $dbConfig->clear('concrete.marketplace.token');
                        $dbConfig->clear('concrete.marketplace.url_token');
                    }
                } else {
                    $this->isConnected = false;
                    $this->connectionError = self::E_GENERAL_CONNECTION_ERROR;
                }
            }
        }
    }

    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $m = __CLASS__;
            $instance = new $m;
        }
        return $instance;
    }

    public static function downloadRemoteFile($file)
    {
        $fh = Loader::helper('file');
        $file .= '?csiURL=' . urlencode(\Core::getApplicationURL()) . "&csiVersion=" . APP_VERSION;
        $pkg = $fh->getContents($file);
        if (empty($pkg)) {
            return Package::E_PACKAGE_DOWNLOAD;
        }
        if ($pkg == Package::E_PACKAGE_INVALID_APP_VERSION) {
            return Package::E_PACKAGE_INVALID_APP_VERSION;
        }

        $file = time();
        // Use the same method as the Archive library to build a temporary file name.
        $tmpFile = $fh->getTemporaryDirectory() . '/' . $file . '.zip';
        $fp = fopen($tmpFile, "wb");
        if ($fp) {
            fwrite($fp, $pkg);
            fclose($fp);
        } else {
            return Package::E_PACKAGE_SAVE;
        }

        return $file;
    }

    /**
     * Runs through all packages on the marketplace, sees if they're installed here, and updates the available version number for them
     */
    public static function checkPackageUpdates()
    {
        $items = Marketplace::getAvailableMarketplaceItems(false);
        foreach ($items as $i) {
            $p = Package::getByHandle($i->getHandle());
            if (is_object($p)) {
                $p->updateAvailableVersionNumber($i->getVersion());
            }
        }
    }

    public function getAvailableMarketplaceItems($filterInstalled = true)
    {

        $fh = Loader::helper('file');
        if (!$fh) {
            return array();
        }

        $dbConfig = \Core::make('config/database');

        // Retrieve the URL contents
        $csToken = $dbConfig->get('concrete.marketplace.token');
        $csiURL = urlencode(\Core::getApplicationURL());
        $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.purchases');
        $url .= "?csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
        $json = $fh->getContents($url);

        $addons = array();

        $objects = @Loader::helper('json')->decode($json);
        if (is_array($objects)) {
            try {
                foreach ($objects as $addon) {
                    $mi = new RemoteItem();
                    $mi->setPropertiesFromJSONObject($addon);
                    $remoteCID = $mi->getRemoteCollectionID();
                    if (!empty($remoteCID)) {
                        $addons[$mi->getHandle()] = $mi;
                    }
                }
            } catch (Exception $e) {
            }

            if ($filterInstalled && is_array($addons)) {

                $handles = Package::getInstalledHandles();
                if (is_array($handles)) {
                    $adlist = array();
                    foreach ($addons as $key => $ad) {
                        if (!in_array($ad->getHandle(), $handles)) {
                            $adlist[$key] = $ad;
                        }
                    }
                    $addons = $adlist;
                }
            }
        }

        return $addons;
    }

    public function getConnectionError()
    {
        return $this->connectionError;
    }

    public function getSitePageURL()
    {
        $dbConfig = \Core::make('config/database');
        $token = $dbConfig->get('concrete.marketplace.url_token');
        $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.site_page');
        return $url . '/' . $token;
    }

    public function getMarketplaceFrame($width = '100%', $height = '300', $completeURL = false, $connectMethod = 'view')
    {
        // if $mpID is passed, we are going to either
        // a. go to its purchase page
        // b. pass you through to the page AFTER connecting.
        $tp = new TaskPermission();
        $frameURL = Config::get('concrete.urls.concrete5');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $frameURL = Config::get('concrete.urls.concrete5_secure');
        }
        if ($tp->canInstallPackages()) {
            if (!$this->isConnected()) {
                if (!$completeURL) {
                    $completeURL = URL::to('/dashboard/extend/connect', 'connect_complete');
                }
                $csReferrer = urlencode($completeURL);
                $csiURL = urlencode(\Core::getApplicationURL());
                // this used to be the BASE_URL and not BASE_URL . DIR_REL but I don't have a method for that
                // and honestly I'm not sure why it needs to be that way
                $csiBaseURL = urlencode(\Core::getApplicationURL());
                if ($this->hasConnectionError()) {
                    if ($this->connectionError == Marketplace::E_DELETED_SITE_TOKEN) {
                        $connectMethod = 'view';
                        $csToken = Marketplace::generateSiteToken();
                    } else {
                        $csToken = $this->getSiteToken();
                    }
                } else {
                    // new connection
                    $csToken = Marketplace::generateSiteToken();
                }
                $url = $frameURL . Config::get('concrete.urls.paths.marketplace.connect') . '/-/' . $connectMethod;
                $url = $url . '?ts=' . time() . '&csiBaseURL=' . $csiBaseURL . '&csiURL=' . $csiURL . '&csToken=' . $csToken . '&csReferrer=' . $csReferrer . '&csName=' . htmlspecialchars(
                        Config::get('concrete.site'),
                        ENT_QUOTES,
                        APP_CHARSET);
            } else {
                $csiBaseURL = urlencode(\Core::getApplicationURL());
                $url = $frameURL . Config::get('concrete.urls.paths.marketplace.connect_success') . '?csToken=' . $this->getSiteToken() . '&csiBaseURL=' . $csiBaseURL;
            }
            if ($csToken == false && !$this->isConnected()) {
                return '<div class="ccm-error">' . t(
                    'Unable to generate a marketplace token. Please ensure that allow_url_fopen is turned on, or that cURL is enabled on your server. If these are both true, It\'s possible your site\'s IP address may be blacklisted for some reason on our server. Please ask your webhost what your site\'s outgoing cURL request IP address is, and email it to us at <a href="mailto:help@concrete5.org">help@concrete5.org</a>.') . '</div>';
            } else {
                $time = time();
                $ifr = '<script type="text/javascript">
                    window.addEventListener("message", function(e) {
                        jQuery.fn.dialog.hideLoader();
                        if (e.data == "loading") {
                            jQuery.fn.dialog.showLoader();
                        } else {
                            var eh = e.data;
                            eh = parseInt(eh) + 100;
                            $("#ccm-marketplace-frame-' . $time . '").attr("height", eh);
                        }
                        });
                    </script>';
                $ifr .= '<iframe class="ccm-marketplace-frame-connect" id="ccm-marketplace-frame-' . $time . '" frameborder="0" width="' . $width . '" height="' . $height . '" src="' . $url . '"></iframe>';
                return $ifr;
            }
        } else {
            return '<div class="ccm-error">' . t(
                'You do not have permission to connect this site to the marketplace.') . '</div>';
        }
    }

    public function isConnected()
    {
        return $this->isConnected;
    }

    public function hasConnectionError()
    {
        return $this->connectionError != false;
    }

    public function generateSiteToken()
    {
        $fh = Loader::helper('file');
        $token = $fh->getContents(Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.connect_new_token'));
        return $token;
    }

    public function getSiteToken()
    {
        $dbConfig = \Core::make('config/database');
        $token = $dbConfig->get('concrete.marketplace.token');
        return $token;
    }

    public function getMarketplacePurchaseFrame($mp, $width = '100%', $height = '530')
    {
        $tp = new TaskPermission();
        if ($tp->canInstallPackages()) {
            if (!is_object($mp)) {
                return '<div class="alert-message block-message error">' . t(
                    'Unable to get information about this product.') . '</div>';
            }
            if ($this->isConnected()) {
                $url = Config::get('concrete.urls.concrete5_secure') . Config::get('concrete.urls.paths.marketplace.checkout');
                $csiURL = urlencode(\Core::getApplicationURL());
                $csiBaseURL = urlencode(\Core::getApplicationURL());
                $csToken = $this->getSiteToken();
                $url = $url . '/' . $mp->getProductBlockID() . '?ts=' . time() . '&csiBaseURL=' . $csiBaseURL . '&csiURL=' . $csiURL . '&csToken=' . $csToken;
            }

            $time = time();
            $ifr = '<script type="text/javascript">
                window.addEventListener("message", function(e) {
                    jQuery.fn.dialog.hideLoader();
                    if (e.data == "loading") {
                        jQuery.fn.dialog.showLoader();
                    } else {
                        var eh = e.data;
                        eh = parseInt(eh) + 100;
                        $("#ccm-marketplace-frame-' . $time . '").attr("height", eh);
                    }
                    });
                </script>';
            $ifr .= '<iframe class="ccm-marketplace-frame" id="ccm-marketplace-frame-' . $time . '" frameborder="0" width="' . $width . '" height="' . $height . '" src="' . $url . '"></iframe>';
            return $ifr;
        } else {
            return '<div class="ccm-error">' . t(
                'You do not have permission to connect this site to the marketplace.') . '</div>';
        }
    }

}
