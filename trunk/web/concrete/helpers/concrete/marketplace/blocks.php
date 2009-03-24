<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('block_types');
Loader::model('block_type_remote');

class ConcreteMarketplaceBlocksHelper { 

	function getPreviewableList($filterInstalled=true) {
		if (!function_exists('mb_detect_encoding')) {
			return false;
		}
		
		$blockTypes = Cache::get('marketplace_block_list', false, false, true);
		if (!is_array($blockTypes)) {
			$fh = Loader::helper('file'); 
			// Retrieve the URL contents 
			$xml = $fh->getContents(MARKETPLACE_BLOCK_LIST_WS);
			$blockTypes=array();
			if($xml || strlen($xml)) {
				// Parse the returned XML file
				$enc = mb_detect_encoding($xml);
				$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
				$xmlObj = new SimpleXMLElement($xml);
				foreach($xmlObj->block as $block){
					$blockType = new BlockTypeRemote();
					$blockType->loadFromXML($block);
					$blockTypes[]=$blockType;
				}
			}

			Cache::set('marketplace_block_list', false, $blockTypes, MARKETPLACE_CONTENT_LATEST_THRESHOLD, true);		
		}

		if ($filterInstalled && is_array($blockTypes)) {
			Loader::model('package');
			$handles = Package::getInstalledHandles();
			$btList = array();
			foreach($blockTypes as $bt) {
				if (!in_array($bt->getHandle(), $handles)) {
					$btList[] = $bt;
				}
			}
			$blockTypes = $btList;
		}

		return $blockTypes;
	}

}

?>
