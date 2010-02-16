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

	protected $isPurchase=false;
	protected $price=0.00;	
	protected $remoteCID=0;
	protected $remoteURL='';
	protected $remoteFileURL='';
	protected $remoteIconURL='';

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
	public function getVersion() {return $this->version;}
	public function isPurchase($value=null) {
		if ($value !== null) {
			$this->isPurchase = $value;
		}
		return $this->isPurchase;
	}
	
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
	
	protected static function getRemotePackageObject($method, $identifier) {
		$fh = Loader::helper('file');

		// Retrieve the URL contents 
		$csToken = Config::get('MARKETPLACE_SITE_TOKEN');
		$csiURL = urlencode(BASE_URL . DIR_REL);
		$url = MARKETPLACE_PURCHASES_LIST_WS."?csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
		$xml = $fh->getContents($url);

		try {
			// Parse the returned XML file
			$enc = mb_detect_encoding($xml);
			$xml = mb_convert_encoding($xml, 'UTF-8', $enc); 
			
			libxml_use_internal_errors(true);
			$xmlObj = new SimpleXMLElement($xml);
			foreach($xmlObj->addon as $addon) {
				$mi = new MarketplaceRemoteItem();
				$mi->loadFromXML($addon);
				$mi->isPurchase(1);
				switch($method) {
					case 'mpHandle':
						if ($mi->getHandle() == $identifier) {
							return $mi;
						}
						break;
					default:
						if ($mi->getMarketplaceItemID() == $identifier) {
							return $mi;
						}
						break;
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

?>
