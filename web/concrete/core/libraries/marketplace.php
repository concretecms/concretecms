<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_Marketplace {
	
	const E_INVALID_BASE_URL = 20;
	const E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED = 21;
	const E_UNRECOGNIZED_SITE_TOKEN = 22;
	const E_DELETED_SITE_TOKEN = 31;
	const E_GENERAL_CONNECTION_ERROR = 99;

	protected $isConnected = false;
	protected $connectionError = false;
	
	public static function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$m = __CLASS__;
			$instance = new $m;
		}
		return $instance;
	}
	
	public function __construct() {
		if (defined('ENABLE_MARKETPLACE_SUPPORT') && ENABLE_MARKETPLACE_SUPPORT == false) {
			$this->connectionError = Marketplace::E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED;
			return;
		}

		$csToken = Config::get('MARKETPLACE_SITE_TOKEN');
		if ($csToken != '') {

			$fh = Loader::helper('file');
			$csiURL = urlencode(BASE_URL . DIR_REL);
			$url = MARKETPLACE_URL_CONNECT_VALIDATE."?csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
			$vn = Loader::helper('validation/numbers');
			$r = $fh->getContents($url);
			if ($r == false) {
				$this->isConnected = true;
			} else if ($vn->integer($r)) {
				$this->isConnected = false;
				$this->connectionError = $r;
				
				if ($this->connectionError == Marketplace::E_DELETED_SITE_TOKEN) {
					Config::clear('MARKETPLACE_SITE_TOKEN');
					Config::clear('MARKETPLACE_SITE_URL_TOKEN');					
				}
			} else {
				$this->isConnected = false;
				$this->connectionError = self::E_GENERAL_CONNECTION_ERROR;
			}
		}		
	}
	
	public function isConnected() {
		return $this->isConnected;
	}
	
	public function hasConnectionError() {
		return $this->connectionError != false;
	}
	
	public function getConnectionError() {
		return $this->connectionError;
	}
	
	public function generateSiteToken() {
		$fh = Loader::helper('file');
		$token = $fh->getContents(MARKETPLACE_URL_CONNECT_TOKEN_NEW);
		return $token;	
	}

	public function getSiteToken() {
		$token = Config::get('MARKETPLACE_SITE_TOKEN');
		return $token;
	}
	
	public function getSitePageURL() {
		$token = Config::get('MARKETPLACE_SITE_URL_TOKEN');
		return MARKETPLACE_BASE_URL_SITE_PAGE . '/' . $token;
	}

	public static function downloadRemoteFile($file) {
		$fh = Loader::helper('file');
		$file .= '?csiURL=' . urlencode(BASE_URL . DIR_REL);
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
	
	public function getMarketplaceFrame($width = '100%', $height = '300', $completeURL = false, $connectMethod = 'view') {
		// if $mpID is passed, we are going to either
		// a. go to its purchase page
		// b. pass you through to the page AFTER connecting.
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) {
			if (!$this->isConnected()) {
				if (!$completeURL) {
					$completeURL = BASE_URL . View::url('/dashboard/extend/connect', 'connect_complete');
				}
				$csReferrer = urlencode($completeURL);
				$csiURL = urlencode(BASE_URL . DIR_REL);
				$csiBaseURL = urlencode(BASE_URL);
				if ($this->hasConnectionError()) {
					if ($this->connectionError == E_DELETED_SITE_TOKEN) {
						$connectMethod = 'view';
						$csToken = Marketplace::generateSiteToken();
					} else { 
						$csToken = $this->getSiteToken();
					}
				} else {
					// new connection 
					$csToken = Marketplace::generateSiteToken();
				}
				$url = MARKETPLACE_URL_CONNECT . '/-/' . $connectMethod;
				$url = $url . '?ts=' . time() . '&csiBaseURL=' . $csiBaseURL . '&csiURL=' . $csiURL . '&csToken=' . $csToken . '&csReferrer=' . $csReferrer . '&csName=' . htmlspecialchars(SITE, ENT_QUOTES, APP_CHARSET);
			} else {
				$csiBaseURL = urlencode(BASE_URL);
				$url = MARKETPLACE_URL_CONNECT_SUCCESS . '?csToken=' . $this->getSiteToken() . '&csiBaseURL=' . $csiBaseURL;
			}
			if ($csToken == false && !$this->isConnected()) {
				return '<div class="ccm-error">' . t('Unable to generate a marketplace token. Please ensure that allow_url_fopen is turned on, or that cURL is enabled on your server. If these are both true, It\'s possible your site\'s IP address may be blacklisted for some reason on our server. Please ask your webhost what your site\'s outgoing cURL request IP address is, and email it to us at <a href="mailto:help@concrete5.org">help@concrete5.org</a>.') . '</div>';
			} else {
				$time = time();
				$ifr = '<script type="text/javascript">$(function() { $.receiveMessage(function(e) { 
					jQuery.fn.dialog.hideLoader();

					if (e.data == "loading") {
						jQuery.fn.dialog.showLoader();
					} else { 
						var eh = e.data;
						eh = parseInt(eh) + 20;
						$("#ccm-marketplace-frame-' . $time . '").attr("height", eh); 
					}
					
					}, \'' . CONCRETE5_ORG_URL . '\');	
				});	
				</script>';
				$ifr .= '<iframe class="ccm-marketplace-frame-connect" id="ccm-marketplace-frame-' . $time . '" frameborder="0" width="' . $width . '" height="' . $height . '" src="' . $url . '"></iframe>';
				return $ifr;
			}
		} else {
			return '<div class="ccm-error">' . t('You do not have permission to connect this site to the marketplace.') . '</div>';
		}
	}

	public function getMarketplacePurchaseFrame($mp, $width = '100%', $height = '530') {
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) {
			if (!is_object($mp)) {
				return '<div class="alert-message block-message error">' . t('Unable to get information about this product.') . '</div>';
			}
			if ($this->isConnected()) {
				$url = MARKETPLACE_URL_CHECKOUT;
				$csiURL = urlencode(BASE_URL . DIR_REL);
				$csiBaseURL = urlencode(BASE_URL);
				$csToken = $this->getSiteToken();
				$url = $url . '/' . $mp->getProductBlockID() . '?ts=' . time() . '&csiBaseURL=' . $csiBaseURL . '&csiURL=' . $csiURL . '&csToken=' . $csToken;
			}
			$time = time();
			$ifr = '<script type="text/javascript">$(function() { $.receiveMessage(function(e) { 
				jQuery.fn.dialog.hideLoader();

				if (e.data == "loading") {
					jQuery.fn.dialog.showLoader();
				} else { 
					var eh = e.data;
					eh = parseInt(eh) + 20;
					$("#ccm-marketplace-frame-' . $time . '").attr("height", eh); 
				}
				
				}, \'' . CONCRETE5_ORG_URL_SECURE . '\');	
			});	
			</script>';
			$ifr .= '<iframe class="ccm-marketplace-frame" id="ccm-marketplace-frame-' . $time . '" class="ccm-marketplace-frame" frameborder="0" width="' . $width . '" height="' . $height . '" src="' . $url . '"></iframe>';
			return $ifr;
		} else {
			return '<div class="ccm-error">' . t('You do not have permission to connect this site to the marketplace.') . '</div>';
		}
	}
	
	
	/** 
	 * Runs through all packages on the marketplace, sees if they're installed here, and updates the available version number for them
	 */
	public static function checkPackageUpdates() {
		Loader::model('system_notification');
		$items = Marketplace::getAvailableMarketplaceItems(false);
		foreach($items as $i) {
			$p = Package::getByHandle($i->getHandle());
			if (is_object($p)) {
				// we only add a notification if it's newer than the last one we know about
				if (version_compare($p->getPackageVersionUpdateAvailable(), $i->getVersion(), '<') && version_compare($p->getPackageVersion(), $i->getVersion(), '<')) {
					SystemNotification::add(SystemNotification::SN_TYPE_ADDON_UPDATE, t('An updated version of %s is available.', $i->getName()), t('New Version: %s.', $i->getVersion()), '', View::url('/dashboard/extend/update'), $i->getRemoteURL());
				}
				$p->updateAvailableVersionNumber($i->getVersion());
			}
		}
	}

	public function getAvailableMarketplaceItems($filterInstalled=true) {
		Loader::model('marketplace_remote_item');
		
		$fh = Loader::helper('file'); 
		if (!$fh) return array();

		// Retrieve the URL contents 
		$csToken = Config::get('MARKETPLACE_SITE_TOKEN');
		$csiURL = urlencode(BASE_URL . DIR_REL);
		$url = MARKETPLACE_PURCHASES_LIST_WS."?csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
		$json = $fh->getContents($url);

		$addons=array();
		
		$objects = @Loader::helper('json')->decode($json);
		if (is_array($objects)) {
			try {
				foreach($objects as $addon){
					$mi = new MarketplaceRemoteItem();
					$mi->setPropertiesFromJSONObject($addon);
					$remoteCID = $mi->getRemoteCollectionID();
					if (!empty($remoteCID)) {
						$addons[$mi->getHandle()] = $mi;
					}
				}
			} catch (Exception $e) {}
	
			if ($filterInstalled && is_array($addons)) {
				Loader::model('package');
				$handles = Package::getInstalledHandles();
				if (is_array($handles)) {
					$adlist = array();
					foreach($addons as $key=>$ad) {
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

}

?>