<?php 

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

	function loadFromXML( $options=array() ){
		if($options['name']) $this->btName=(string) $options['name'];
		//if($options['cID']) $this->cID=$options['cID'];
		if($options['handle']) $this->btHandle= (string) $options['handle'];
		if($options['description']) $this->btDescription= (string) $options['description'];
		if($options['url']) $this->remoteURL= (string) $options['url']; 
		if($options['icon']) $this->remoteIconURL= (string) $options['icon']; 
		if($options['price']) $this->price= (string) $options['price']; 
	}	

	public function getPrice(){ return sprintf("%.2f",floatval($this->price)); }
	public function getRemoteURL(){ return $this->remoteURL; }
	public function getRemoteIconURL(){ return $this->remoteIconURL; }
}	

?>