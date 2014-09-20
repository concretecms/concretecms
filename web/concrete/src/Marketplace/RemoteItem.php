<?php
namespace Concrete\Core\Marketplace;
use Loader;
use Config;
use \Concrete\Core\Foundation\Object;
class RemoteItem extends Object {

	protected $price=0.00;
	protected $remoteCID=0;
	protected $remoteURL='';
	protected $remoteFileURL='';
	protected $remoteIconURL='';
	protected $isLicensedToSite = false;

	public function setPropertiesFromJSONObject($obj) {
		foreach($obj as $prop => $value) {
			$this->{$prop} = $value;
		}
	}

	public function getMarketplaceItemID() {return $this->mpID;}
	public function getMarketplaceItemType() {return $this->mpType;}
	public function getHandle() { return $this->handle; }
	public function getName(){ return $this->name; }
	public function getDescription() {return $this->description;}
	public function getBody() {return $this->bodyContent;}
	public function getPrice(){ return sprintf("%.2f",floatval($this->price)); }
	public function getScreenshots() {
		if (is_array($this->screenshots)) {
			return $this->screenshots;
		} else {
			return array();
		}
	}
	public function getMarketplaceItemVersionForThisSite() {return $this->siteLatestAvailableVersion;}

	public function getAverageRating() {return $this->rating;}
	public function getVersionHistory() {return $this->versionHistory;}
	public function getTotalRatings() {
		if ($this->totalRatings) {
			return $this->totalRatings;
		} else {
			return 0;
		}
	}
	public function getRemoteReviewsURL() {return $this->reviewsURL;}
	public function getRemoteCollectionID(){ return $this->cID; }
	public function getReviewBody() {
		return $this->reviewBody;
	}
	public function getLargeThumbnail() {
		if ($this->largethumbnail) {
			return $this->largethumbnail;
		} else {
			$screenshots = $this->getScreenshots();
			return $screenshots[0];
		}
	}
	public function getRemoteURL(){ return $this->url; }
	public function getProductBlockID() {return $this->productBlockID;}
	public function getFivePackProductBlockID() {return $this->fivePackProductBlockID;}
	public function getRemoteFileURL(){ return $this->file; }
	public function getRemoteIconURL(){ return $this->icon; }
	public function getRemoteListIconURL() {return $this->listicon;}
	public function isLicensedToSite() {return $this->islicensed;}
	public function purchaseRequired() {
		if ($this->price == '' || $this->price == '0' || $this->price == '0.00') {
			return false;
		} else if ($this->isLicensedToSite()) {
			return false;
		} else {
			return true;
		}
	}

	public function getVersion() {return $this->pkgVersion;}

	public function downloadUpdate() {
		$pkg = Package::getByHandle($this->getHandle());

		$fileURL = $this->getRemoteFileURL();
		if (empty($fileURL)) {
			return array(Package::E_PACKAGE_NOT_FOUND);
		}

		$file = Marketplace::downloadRemoteFile($this->getRemoteFileURL());
		if (empty($file) || $file == Package::E_PACKAGE_DOWNLOAD) {
			return array(Package::E_PACKAGE_DOWNLOAD);
		} else if ($file == Package::E_PACKAGE_SAVE) {
			return array($file);
		}

		$r = $pkg->backup();
		if (is_array($r)) {
			return $r;
		}

		try {

			$am = new PackageArchive($this->getHandle());
			$am->install($file, true);
		} catch (Exception $e) {
			$pkg->restore();
			return array($e->getMessage());
		}

	}

	public function download() {
		$file = Marketplace::downloadRemoteFile($this->getRemoteFileURL());
		if (empty($file) || $file == Package::E_PACKAGE_DOWNLOAD) {
			return array(Package::E_PACKAGE_DOWNLOAD);
		} else if ($file == Package::E_PACKAGE_SAVE) {
			return array($file);
		} else if ($file == Package::E_PACKAGE_INVALID_APP_VERSION) {
			return array($file);
		}

		try {

			$am = new PackageArchive($this->getHandle());
			$am->install($file, true);
		} catch (Exception $e) {
			return array($e->getMessage());
		}

		if ($install) {
			$tests = Package::testForInstall($this->getHandle());
			if (is_array($tests)) {
				return $tests;
			} else {
				$p = Loader::package($this->getHandle());
				try {
					$p->install();
				} catch(Exception $e) {
					return array(Package::E_PACKAGE_INSTALL);
				}
			}
		}
	}

	public function enableFreeLicense() {
		$fh = Loader::helper('file');
		$csToken = Config::get('concrete.marketplace.token');
		$csiURL = urlencode(BASE_URL . DIR_REL);
        $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.item_free_license');
		$url .= "?mpID=" . $this->mpID . "&csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
		$fh->getContents($url);
	}

	protected static function getRemotePackageObject($method, $identifier) {
		$fh = Loader::helper('file');

		// Retrieve the URL contents
		$csToken = Config::get('concrete.marketplace.token');
		$csiURL = urlencode(BASE_URL . DIR_REL);

        $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.item_information');
		$url .= "?" . $method . "=" . $identifier . "&csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
		$json = $fh->getContents($url);

		try {
			// Parse the returned XML file
			$obj = @Loader::helper('json')->decode($json);
			if (is_object($obj)) {
				$mi = new RemoteItem();
				$mi->setPropertiesFromJSONObject($obj);
				if ($mi->getMarketplaceItemID() > 0) {
					return $mi;
				}
			}
		} catch (Exception $e) {
			throw new Exception(t('Unable to connect to marketplace to retrieve item'));
		}
	}

	public static function getByHandle($mpHandle) {
		return RemoteItem::getRemotePackageObject('mpHandle', $mpHandle);
	}

	public static function getByID($mpID) {
		return RemoteItem::getRemotePackageObject('mpID', $mpID);
	}
}
