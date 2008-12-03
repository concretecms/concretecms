<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('block_types');
Loader::model('block_type_remote');

class ConcreteMarketplaceBlocksHelper { 

	function getPreviewableList(){
		$fh = Loader::helper('file'); 
		// Retrieve the URL contents 
		$xml = $fh->getContents(MARKETPLACE_BLOCK_LIST_WS);
		if(!$xml || !strlen($xml)) return array();	
		//echo htmlspecialchars($xml);
		// Parse the returned XML file
		$enc = mb_detect_encoding($xml);
		$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
		$this->xmlObj = new SimpleXMLElement($xml);
		if(!$this->xmlObj) return array();	
		$blockTypes=array();
		foreach($this->xmlObj->block as $block){
			$blockType = new BlockTypeRemote();
			$blockType->loadBlock($block);			
			$blockTypes[]=$blockType;
		}	
		return $blockTypes;
	}

}

?>