<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('block_types');
Loader::model('block_type_remote');

class ConcreteMarketplaceBlocksHelper { 

	function getCombinedList($filterInstalled=true) {
		$previewList = $this->getList('marketplace_previewable_list', $filterInstalled);
		$purchasesList = $this->getList('marketplace_purchases_list', $filterInstalled);

		return array_merge($previewList, $purchasesList);
	}

	function getPreviewableList($filterInstalled=true) {
		return $this->getList('marketplace_previewable_list', $filterInstalled);
	}

	function getPurchasesList($filterInstalled=true) {
		return $this->getList('marketplace_purchases_list', $filterInstalled);
	}

	private function getList($list, $filterInstalled=true) {
		if (!function_exists('mb_detect_encoding')) {
			return array();
		}
		
		$cacheKey = '';
		if ($list == 'marketplace_purchases_list') {
			$authData = UserInfo::getAuthData();
			$cacheKey = $authData['auth_timestamp'];
			if (!isset($authData['auth_token']) || !isset($authData['auth_uname']) || !isset($authData['auth_timestamp'])) {
				return array();
			}
		}

		$blockTypes = Cache::get($list.$cacheKey, false, false, true);
		if (!is_array($blockTypes)) {
			$fh = Loader::helper('file'); 
			if (!$fh) return array();

			// Retrieve the URL contents 
			if ($list == 'marketplace_purchases_list') {
				$url = MARKETPLACE_PURCHASES_LIST_WS."?auth_token={$authData['auth_token']}&auth_uname={$authData['auth_uname']}&auth_timestamp={$authData['auth_timestamp']}";
			} else if ($list == 'marketplace_previewable_list') {
				$url = MARKETPLACE_BLOCK_LIST_WS;
			}

			$xml = $fh->getContents($url);
			$blockTypes=array();
			if( $xml || strlen($xml) ) {
				// Parse the returned XML file
				$enc = mb_detect_encoding($xml);
				$xml = mb_convert_encoding($xml, 'UTF-8', $enc); 
				
				try {
					libxml_use_internal_errors(true);
					$xmlObj = new SimpleXMLElement($xml);
					foreach($xmlObj->block as $block){
						$blockType = new BlockTypeRemote();
						$blockType->loadFromXML($block);
						$blockType->isPurchase($list == 'marketplace_purchases_list' ? 1 : 0);
						$remoteCID = $blockType->getRemoteCollectionID();
						if (!empty($remoteCID)) {
							$blockTypes['cid-'.$remoteCID] = $blockType;
						}
					}
				} catch (Exception $e) {}
			}

			Cache::set($list.$cacheKey, false, $blockTypes, MARKETPLACE_CONTENT_LATEST_THRESHOLD, true);		
		}

		if ($filterInstalled && is_array($blockTypes)) {
			Loader::model('package');
			$handles = Package::getInstalledHandles();
			if (is_array($handles)) {
				$btList = array();
				foreach($blockTypes as $key=>$bt) {
					if (!in_array($bt->getHandle(), $handles)) {
						$btList[$key] = $bt;
					}
				}
				$blockTypes = $btList;
			}
		}

		return $blockTypes;
	}

}

?>
