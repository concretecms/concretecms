<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('page_theme_remote');

class ConcreteMarketplaceThemesHelper  {

	function getPreviewableList(){
		$fh = Loader::helper('file'); 
		// Retrieve the URL contents 
		$xml = $fh->getContents(MARKETPLACE_THEME_LIST_WS);
		if(!$xml || !strlen($xml)) return array();	
		//echo htmlspecialchars($xml);
		// Parse the returned XML file
		$enc = mb_detect_encoding($xml);
		$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
		$this->xmlObj = new SimpleXMLElement($xml);
		if(!$this->xmlObj) return array();	
		$pageThemes=array();
		foreach($this->xmlObj->theme as $theme){
			$pgTheme = new PageThemeRemote();
			$pgTheme->loadTheme($theme);
			$pageThemes[]=$pgTheme;
		}	
		return $pageThemes;
	}





}

?>