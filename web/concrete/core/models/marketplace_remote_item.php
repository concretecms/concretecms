<?

/**
*
* For loading an external block from the marketplace
* @author Tony Trupp <tony@concrete5.org>
* @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
* @license    http://www.concrete5.org/license/     MIT License
* @package Blocks
* @category Concrete
*/

class Concrete5_Model_MarketplaceRemoteItem extends Object {

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
		// backup the old package
		$pkg = Package::getByHandle($this->getHandle());
		$r = $pkg->backup();
		if (is_array($r)) {
			return $r;
		}

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
			
		try {
			Loader::model('package_archive');
			$am = new PackageArchive($this->getHandle());
			$am->install($file, true);
		} catch (Exception $e) {
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
			Loader::model('package_archive');
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
		$csToken = Config::get('MARKETPLACE_SITE_TOKEN');
		$csiURL = urlencode(BASE_URL . DIR_REL);
		$url = MARKETPLACE_ITEM_FREE_LICENSE_WS."?mpID=" . $this->mpID . "&csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
		$fh->getContents($url);
	}
	
	protected static function getRemotePackageObject($method, $identifier) {
		$fh = Loader::helper('file');

		// Retrieve the URL contents 
		$csToken = Config::get('MARKETPLACE_SITE_TOKEN');
		$csiURL = urlencode(BASE_URL . DIR_REL);
		$url = MARKETPLACE_ITEM_INFORMATION_WS."?" . $method . "=" . $identifier . "&csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
		$json = $fh->getContents($url);

		try {
			// Parse the returned XML file
			$obj = @Loader::helper('json')->decode($json);
			if (is_object($obj)) {
				$mi = new MarketplaceRemoteItem();
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
		return MarketplaceRemoteItem::getRemotePackageObject('mpHandle', $mpHandle);
	}
	
	public static function getByID($mpID) {
		return MarketplaceRemoteItem::getRemotePackageObject('mpID', $mpID);
	}
}

class Concrete5_Model_MarketplaceRemoteItemList extends ItemList {
	
	protected $includeInstalledItems = true;
	protected $params = array();
	protected $type = 'themes';
	protected $itemsPerPage = 20;
	
	public static function getItemSets($type) {
		$url = MARKETPLACE_REMOTE_ITEM_LIST_WS;
		$url .= $type . '/-/get_remote_item_sets';
		$contents = Loader::helper("file")->getContents($url);
		$sets = array();
		if ($contents != '') {
			$objects = @Loader::helper('json')->decode($contents);
			if (is_array($objects)) {
				foreach($objects as $obj) {
					$mr = new MarketplaceRemoteItemSet();
					$mr->id = $obj->marketplaceItemSetID;
					$mr->name = $obj->marketplaceItemSetName;
					$sets[] = $mr;
				}
			}
		}
		return $sets;
	}	
	
	public function setIncludeInstalledItems($pp) {
		$this->includeInstalledItems = $pp;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function filterByKeywords($keywords) {
		$this->params['keywords'] = $keywords;
	}

	public function filterByMarketplaceItemID($mpID) {
		$this->params['mpID'] = $mpID;
	}
	
	public function sortBy($sortBy) {
		$this->params['sort'] = $sortBy;
	}
	
	public function filterBySet($set) {
		$this->params['set'] = $set;
	}

	public function filterByIsFeaturedRemotely($r) {
		$this->params['is_featured_remotely'] = $r;
	}
	
	public function filterByCompatibility($r) {
		$this->params['is_compatible'] = $r;
	}
	
	public function execute() {
		$params = $this->params;
		$params['version'] = APP_VERSION;
		$params['itemsPerPage'] = $this->itemsPerPage;
		Loader::library("marketplace");
		$mi = Marketplace::getInstance();
		$params['csToken'] = $mi->getSiteToken();
		
		if ($this->includeInstalledItems) {
			$params['includeInstalledItems'] = 1;
		} else {
			$params['includeInstalledItems'] = 0;
			$list = Package::getInstalledList();
			foreach($list as $pkg) {
				$params['installedPackages'][] = $pkg->getPackageHandle();
			}
		}

		if (isset($_REQUEST[$this->queryStringPagingVariable])) {
			$params[$this->queryStringPagingVariable] = $_REQUEST[$this->queryStringPagingVariable];
		}
		
		$uh = Loader::helper('url');
		
		$url = $uh->buildQuery(MARKETPLACE_REMOTE_ITEM_LIST_WS . $this->type . '/-/get_remote_list', $params);
		$r = Loader::helper('file')->getContents($url);
		$r2 = @Loader::helper('json')->decode($r);
				
		$total = 0;
		$items = array();
		
		if (is_object($r2)) {
			$items = $r2->items;
			$total = $r2->total;
		}

		$this->total = $total;
		$this->setItems($items);
	}
	
	public function get($itemsToGet = 0, $offset = 0) {
		$this->start = $offset;
		$items = $this->items;
		$marketplaceItems = array();
		foreach($items as $stdObj) {
			$mi = new MarketplaceRemoteItem();
			$mi->setPropertiesFromJSONObject($stdObj);
			$marketplaceItems[] = $mi;
		}
		return $marketplaceItems;
	}
	
	
	
}

class Concrete5_Model_MarketplaceRemoteItemSet extends Object {
	
	public function getMarketplaceRemoteSetName() {return $this->name;}
	public function getMarketplaceRemoteSetID() {return $this->id;}
	

}