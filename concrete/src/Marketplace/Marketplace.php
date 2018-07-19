<?php
namespace Concrete\Core\Marketplace;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Service\File;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use Concrete\Core\Support\Facade\Package;
use Concrete\Core\Url\Resolver\PathUrlResolver;
use Zend\Http\Client\Adapter\Exception\TimeoutException;
use Exception;

class Marketplace implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    const E_INVALID_BASE_URL = 20;
    const E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED = 21;
    const E_UNRECOGNIZED_SITE_TOKEN = 22;
    const E_DELETED_SITE_TOKEN = 31;
    const E_CONNECTION_TIMEOUT = 41;
    const E_GENERAL_CONNECTION_ERROR = 99;

    protected $isConnected = false;
    protected $connectionError = false;

    /** @var Repository */
    protected $config;

    /** @var Repository */
    protected $databaseConfig;

    /** @var File */
    protected $fileHelper;

    /** @var PathUrlResolver */
    protected $urlResolver;

    public function setApplication(\Concrete\Core\Application\Application $application)
    {
        $this->app = $application;

        $this->fileHelper = $this->app->make('helper/file');
        $this->config = $this->app->make('config');
        $this->databaseConfig = $this->app->make('config/database');
        $this->urlResolver = $this->app->make(PathUrlResolver::class);
        $this->isConnected = false;

        $this->isConnected();
    }

    /**
     * @param $dbConfig
     */
    public function isConnected()
    {
        if ($this->isConnected) {
            return true;
        }

        if (!$this->config->get('concrete.marketplace.enabled')) {
            $this->connectionError = self::E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED;

            return;
        }

        $csToken = $this->databaseConfig->get('concrete.marketplace.token');

        $this->isConnected = false;

        if ($csToken != '') {
            $fh = $this->app->make('helper/file');
            $csiURL = urlencode($this->getSiteURL());
            $url = $this->config->get('concrete.urls.concrete5') . $this->config->get('concrete.urls.paths.marketplace.connect_validate') . "?csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
            $vn = $this->app->make('helper/validation/numbers');
            $r = $this->get($url);

            if ($r === null) {
                $this->isConnected = true;
            } else {
                if ($vn->integer($r)) {
                    $this->isConnected = false;
                    $this->connectionError = $r;

                    if ($this->connectionError == self::E_DELETED_SITE_TOKEN) {
                        $dbConfig->clear('concrete.marketplace.token');
                        $dbConfig->clear('concrete.marketplace.url_token');
                    }
                } else {
                    $this->isConnected = false;
                    $this->connectionError = self::E_GENERAL_CONNECTION_ERROR;
                }
            }
        }

        return $this->isConnected;
    }

    /**
     * Get the contents of a URL
     * @param $url
     * @return string|null
     */
    private function get($url)
    {
        try {
            $result = $this->fileHelper->getContents(
                $url,
                $this->config->get('concrete.marektplace.request_timeout'));
        } catch (TimeoutException $e) {
            // Catch a timeout
            $this->connectionError = self::E_CONNECTION_TIMEOUT;

            return null;
        } catch (Exception $e) {
            $this->connectionError = self::E_GENERAL_CONNECTION_ERROR;

            return null;
        }

        return $result ?: null;
    }

    /**
     * @return static|Marketplace
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = Application::make(__CLASS__);
        }

        return $instance;
    }

    /**
     * @param $file
     * @return int|mixed|string
     */
    public static function downloadRemoteFile($file)
    {
        // Get the marketplace instance
        $marketplace = static::getInstance();
        $file .= '?csiURL=' . urlencode($marketplace->getSiteURL()) . "&csiVersion=" . APP_VERSION;

        // Retreive the package
        $pkg = $marketplace->get($file);

        $error = $marketplace->app->make('error');
        if (empty($pkg)) {
            $error->add(t('An error occurred while downloading the package.'));
        }
        if ($pkg == \Package::E_PACKAGE_INVALID_APP_VERSION) {
            $error->add(t('This package isn\'t currently available for this version of concrete5 . Please contact the maintainer of this package for assistance.'));
        }

        $file = time();
        // Use the same method as the Archive library to build a temporary file name.
        $tmpFile = $marketplace->fileHelper->getTemporaryDirectory() . '/' . $file . '.zip';
        $fp = fopen($tmpFile, "wb");
        if ($fp) {
            fwrite($fp, $pkg);
            fclose($fp);
        } else {
            $error->add(t('concrete5 was not able to save the package after download.'));
        }

        if ($error->has()) {
            return $error;
        }

        return $file;
    }

    /**
     * Runs through all packages on the marketplace, sees if they're installed here, and updates the available version number for them.
     */
    public static function checkPackageUpdates()
    {
        $em = \ORM::entityManager();
        $items = self::getAvailableMarketplaceItems(false);
        foreach ($items as $i) {
            $p = Package::getByHandle($i->getHandle());
            if (is_object($p)) {
                /**
                 * @var $p \Concrete\Core\Entity\Package
                 */
                $p->setPackageAvailableVersion($i->getVersion());
                $em->persist($p);
            }
        }
        $em->flush();
    }

    public static function getAvailableMarketplaceItems($filterInstalled = true)
    {
        $marketplace = static::getInstance();

        $fh = $marketplace->fileHelper;
        if (!$fh) {
            return array();
        }

        // Retrieve the URL contents
        $csToken = $marketplace->databaseConfig->get('concrete.marketplace.token');
        $csiURL = urlencode($marketplace->getSiteURL());
        $url = $marketplace->config->get('concrete.urls.concrete5') . $marketplace->config->get('concrete.urls.paths.marketplace.purchases');
        $url .= "?csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
        $json = $marketplace->get($url);

        $addons = array();

        $objects = @$marketplace->app->make('helper/json')->decode($json);
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
        $dbConfig = $this->app->make('config/database');
        $token = $dbConfig->get('concrete.marketplace.url_token');
        $url = $this->config->get('concrete.urls.concrete5') . $this->config->get('concrete.urls.paths.site_page');

        return $url . '/' . $token;
    }

    public function getMarketplaceFrame($width = '100%', $height = '300', $completeURL = false, $connectMethod = 'view')
    {
        // if $mpID is passed, we are going to either
        // a. go to its purchase page
        // b. pass you through to the page AFTER connecting.
        $tp = new TaskPermission();
        $frameURL = $this->config->get('concrete.urls.concrete5');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $frameURL = $this->config->get('concrete.urls.concrete5_secure');
        }
        if ($tp->canInstallPackages()) {
            $csToken = null;
            if (!$this->isConnected()) {
                if (!$completeURL) {
                    $completeURL = $this->urlResolver->resolve(['/dashboard/extend/connect', 'connect_complete']);
                    $completeURL = $completeURL->setQuery(array(
                        'ccm_token' => $this->app->make('token')->generate('marketplace/connect'),
                    ));
                }
                $csReferrer = urlencode($completeURL);
                $csiURL = urlencode($this->getSiteURL());

                // this used to be the BASE_URL and not BASE_URL . DIR_REL but I don't have a method for that
                // and honestly I'm not sure why it needs to be that way
                $csiBaseURL = $csiURL;

                if ($this->hasConnectionError()) {
                    if ($this->connectionError == self::E_DELETED_SITE_TOKEN) {
                        $connectMethod = 'view';
                        $csToken = self::generateSiteToken();

                        if (!$csToken && $this->connectionError === self::E_CONNECTION_TIMEOUT) {
                            return '<div class="ccm-error">' .
                                t('Unable to generate a marketplace token. Request timed out.') .
                                '</div>';
                        }
                    } else {
                        $csToken = $this->getSiteToken();
                    }
                } else {
                    // new connection
                    $csToken = self::generateSiteToken();

                    if (!$csToken && $this->connectionError === self::E_CONNECTION_TIMEOUT) {
                        return '<div class="ccm-error">' .
                        t('Unable to generate a marketplace token. Request timed out.') .
                        '</div>';
                    }
                }

                $url = $frameURL . $this->config->get('concrete.urls.paths.marketplace.connect') . '/-/' . $connectMethod;
                $url = $url . '?ts=' . time() . '&csiBaseURL=' . $csiBaseURL . '&csiURL=' . $csiURL . '&csToken=' . $csToken . '&csReferrer=' . $csReferrer . '&csName=' . htmlspecialchars(
                        $this->app->make('site')->getSite()->getSiteName(),
                        ENT_QUOTES,
                        APP_CHARSET);
            } else {
                $csiBaseURL = urlencode($this->getSiteURL());
                $url = $frameURL . $this->config->get('concrete.urls.paths.marketplace.connect_success') . '?csToken=' . $this->getSiteToken() . '&csiBaseURL=' . $csiBaseURL;
            }

            if (!$csToken && !$this->isConnected()) {
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

    public function hasConnectionError()
    {
        return $this->connectionError != false;
    }

    /**
     * @return bool|string
     */
    public function generateSiteToken()
    {
        return $this->get(
            $this->config->get('concrete.urls.concrete5') .
            $this->config->get('concrete.urls.paths.marketplace.connect_new_token'));
    }

    public static function getSiteToken()
    {
        $marketplace = static::getInstance();

        $dbConfig = $marketplace->app->make('config/database');
        $token = $dbConfig->get('concrete.marketplace.token');

        return $token;
    }

    public function getSiteURL()
    {
        $url = $this->app->make('url/canonical');
        $url = rtrim((string) $url, '/');
        return $url;
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
                $url = $this->config->get('concrete.urls.concrete5_secure') . $this->config->get('concrete.urls.paths.marketplace.checkout');
                $csiURL = urlencode($this->getSiteURL());
                $csiBaseURL = $csiURL;
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
