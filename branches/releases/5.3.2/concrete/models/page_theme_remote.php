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
	
	protected $remoteFileURL='';
	protected $remoteThumbURL='';
	protected $cID='';
	protected $price=0.00;	
	
	// we have to explicitly cast these as their types otherwise they get serialized as simplexmlelement and we can't retrieve
	// them from the cache easily
	function loadFromXML( $options=array() ){
		if($options['name']) $this->ptName= (string) $options['name'];
		if($options['cID']) $this->cID= (int) $options['cID'];
		if($options['handle']) $this->ptHandle= (string) $options['handle'];
		if($options['description']) $this->ptDescription = (string)  $options['description'];
		if($options['url']) $this->ptURL = (string) $options['url']; 
		if($options['file']) $this->remoteFileURL = (string) $options['file']; 
		if($options['thumbnail']) $this->remoteThumbURL = (string) $options['thumbnail']; 
		if($options['price']) $this->ptPrice = (string) $options['price']; 
	}	
	public function getHandle() { return $this->ptHandle; }
	public function getName(){ return $this->ptName; }
	public function getPrice(){ return sprintf("%.2f",floatval($this->ptPrice)); }
	public function getRemoteCollectionID(){ return $this->cID; }
	public function getRemoteURL(){ return $this->ptURL; }
	public function getRemoteFileURL(){ return $this->remoteFileURL; }
	public function getThemeDescription() { return $this->ptDescription; }
	public function getThemeName() { return $this->ptName; }
	public function getThemeThumbnail() {
		if($this->remoteThumbURL)
			return $this->remoteThumbURL;
		return parent::getThemeThumbnail();
	}	
}

?>
