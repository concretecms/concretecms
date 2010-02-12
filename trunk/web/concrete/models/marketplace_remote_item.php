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
}	

?>
