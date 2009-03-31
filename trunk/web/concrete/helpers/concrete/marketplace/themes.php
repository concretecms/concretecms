<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('page_theme_remote');

class ConcreteMarketplaceThemesHelper  {

	function getPreviewableList($filterInstalled=true) {
		if (!function_exists('mb_detect_encoding')) {
			return false;
		}
		
		$pageThemes = Cache::get('marketplace_theme_list', false, false, true);
		if (!is_array($pageThemes)) {
			$fh = Loader::helper('file'); 
			// Retrieve the URL contents 
			$xml = $fh->getContents(MARKETPLACE_THEME_LIST_WS);
			$pageThemes=array();
			if( !strstr($xml,'<title>404 Not Found</title>') && ($xml || strlen($xml))  ) {
				// Parse the returned XML file
				$enc = mb_detect_encoding($xml);
				$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
				$xmlObj = new SimpleXMLElement($xml);
				foreach($xmlObj->theme as $theme){
					$pgTheme = new PageThemeRemote();
					$pgTheme->loadFromXML($theme);
					$pageThemes[]=$pgTheme;
				}
			}
			Cache::set('marketplace_theme_list', false, $pageThemes, MARKETPLACE_CONTENT_LATEST_THRESHOLD, true);		
		} 

		if ($filterInstalled && is_array($pageThemes)) {
			Loader::model('page_theme');
			$handles = PageTheme::getInstalledHandles();
			$ptList = array();
			foreach($pageThemes as $pt) {
				if (!in_array($pt->getHandle(), $handles)) {
					$ptList[] = $pt;
				}
			}
			$pageThemes = $ptList;
		}

		return $pageThemes;
	}

}

?>
