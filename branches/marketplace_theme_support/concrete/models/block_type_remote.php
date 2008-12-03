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

class BlockTypeRemote extends BlockType{

	protected $remoteIconURL='';
	protected $remoteURL='';
	protected $price=0.00;	

	function loadBlock( $options=array() ){
		if($options['name']) $this->btName=$options['name'];
		//if($options['cID']) $this->cID=$options['cID'];
		if($options['handle']) $this->btHandle=$options['handle'];
		if($options['description']) $this->btDescription=$options['description'];
		if($options['url']) $this->remoteURL=$options['url']; 
		if($options['icon']) $this->remoteIconURL=$options['icon']; 
		if($options['price']) $this->price=$options['price']; 
	}	

	public function getPrice(){ return sprintf("%.2f",floatval($this->price)); }
	public function getRemoteURL(){ return $this->remoteURL; }
	public function getRemoteIconURL(){ return $this->remoteIconURL; }
}	

?>