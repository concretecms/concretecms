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

class MarketplaceRemoteItem extends Object {

	protected $price=0.00;	
	protected $remoteCID=0;
	protected $remoteURL='';
	protected $remoteFileURL='';
	protected $remoteIconURL='';
	protected $isLicensedToSite = false;
	
	function loadFromXML( $options=array() ){
		if($options['mpID']) $this->mpID=(string) $options['mpID'];
		if($options['name']) $this->name=(string) $options['name'];
		if($options['cID']) $this->remoteCID=(string) $options['cID'];
		if($options['handle']) $this->handle= (string) $options['handle'];
		if($options['description']) $this->description= (string) $options['description'];
		if($options['url']) $this->remoteURL= (string) $options['url']; 
		if($options['file']) $this->remoteFileURL= (string) $options['file']; 
		if($options['icon']) $this->remoteIconURL= (string) $options['icon']; 
		if($options['price']) $this->price= (string) $options['price']; 
		if($options['version']) $this->version = (string) $options['version'];
		if($options['listicon']) $this->remoteListIconURL = (string) $options['listicon'];
		if($options['islicensed'] == 1) $this->isLicensedToSite = true;
	}	

	public function getMarketplaceItemID() {return $this->mpID;}
	public function getHandle() { return $this->handle; }
	public function getName(){ return $this->name; }
	public function getDescription() {return $this->description;}
	public function getPrice(){ return sprintf("%.2f",floatval($this->price)); }
	public function getRemoteCollectionID(){ return $this->remoteCID; }
	public function getRemoteURL(){ return $this->remoteURL; }
	public function getRemoteFileURL(){ return $this->remoteFileURL; }
	public function getRemoteIconURL(){ return $this->remoteIconURL; }
	public function getRemoteListIconURL() {return $this->remoteListIconURL;}
	public function isLicensedToSite() {return $this->isLicensedToSite;}
	public function purchaseRequired() {
		if ($this->price == '' || $this->price == '0' || $this->price == '0.00') {
			return false;
		} else if ($this->isLicensedToSite) {
			return false;	
		} else {
			return true;
		}
	}
	
	public function getVersion() {return $this->version;}
	
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
		$xml = $fh->getContents($url);

		try {
			// Parse the returned XML file
			$enc = mb_detect_encoding($xml);
			$xml = mb_convert_encoding($xml, 'UTF-8', $enc); 
			
			libxml_use_internal_errors(true);
			$xmlObj = new SimpleXMLElement($xml);
			$mi = new MarketplaceRemoteItem();
			$mi->loadFromXML($xmlObj);
			if (is_object($mi) && $mi->getMarketplaceItemID() > 0) {
				return $mi;
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

class MarketplaceRemoteItemList extends ItemList {
	
	protected $includePreviouslyPurchasedItems = false;
	
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
	
	public function setInludePreviouslyPurchasedItems($pp) {
		$this->previouslyPurchasedItems = $pp;
	}
	
}

class MarketplaceRemoteItemSet extends Object {
	
	public function getMarketplaceRemoteSetName() {return $this->name;}
	public function getMarketplaceRemoteSetID() {return $this->id;}
	

}