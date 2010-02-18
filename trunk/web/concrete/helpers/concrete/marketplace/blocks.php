<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('marketplace_remote_item');

class ConcreteMarketplaceBlocksHelper { 

	function getPreviewableList($filterInstalled=true) {
		if (!function_exists('mb_detect_encoding')) {
			return array();
		}
		
		$blockTypes = Cache::get('marketplace_block_list', false, false, true);
		if (!is_array($blockTypes)) {
			$fh = Loader::helper('file'); 
			if (!$fh) return array();

			// Retrieve the URL contents 
			$xml = $fh->getContents(MARKETPLACE_BLOCK_LIST_WS);
			$blockTypes=array();
			if( $xml || strlen($xml)  ) {
				// Parse the returned XML file
				$enc = mb_detect_encoding($xml);
				$xml = mb_convert_encoding($xml, 'UTF-8', $enc);

				try {
					libxml_use_internal_errors(true);
					$xmlObj = new SimpleXMLElement($xml);
					foreach($xmlObj->block as $bt) {
						$mri = new MarketplaceRemoteItem();
						$mri->loadFromXML($bt);
						$blockTypes[]=$mri;
					}
				} catch (Exception $e) {}
			}
			Cache::set('marketplace_block_list', false, $blockTypes, MARKETPLACE_CONTENT_LATEST_THRESHOLD, true);		
		} 
		
		if ($filterInstalled && is_array($blockTypes)) {
			$pl = PackageList::get();
			$pkgHandles = array();
			foreach($pl->getPackages() as $pkg) {
				$pkgHandles[] = $pkg->getPackageHandle();
			}
			
			$ptList = array();
			foreach($blockTypes as $pt) {
				if (!in_array($pt->getHandle(), $pkgHandles)) {
					$ptList[] = $pt;
				}
			}

			$blockTypes = $ptList;
		}

		return $blockTypes;
	}

}

?>
