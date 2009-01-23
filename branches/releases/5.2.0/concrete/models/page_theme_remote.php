<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('page_theme');

/**
*
* This class is responsible for unpacking themes that have been zipped and uploaded to the system. 
* @package Pages
* @subpackage Themes
*/
class PageThemeRemote extends PageTheme {
	
	protected $remoteThumbURL='';
	protected $cID='';
	
	// we have to explicitly cast these as their types otherwise they get serialized as simplexmlelement and we can't retrieve
	// them from the cache easily
	function loadFromXML( $options=array() ){
		if($options['name']) $this->ptName= (string) $options['name'];
		if($options['cID']) $this->cID= (int) $options['cID'];
		if($options['handle']) $this->ptHandle= (string) $options['handle'];
		if($options['description']) $this->ptDescription = (string)  $options['description'];
		if($options['url']) $this->ptURL = (string) $options['url']; 
		if($options['thumbnail']) $this->remoteThumbURL = (string) $options['thumbnail']; 
	}	
	public function getRemoteCollectionID(){ return $this->cID; }
	public function getThemeThumbnail() {
		if($this->remoteThumbURL)
			return $this->remoteThumbURL;
		return parent::getThemeThumbnail();
	}	
}

?>