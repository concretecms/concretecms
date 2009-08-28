<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Contains the collection attribute key and value objects.
 * @package models
 * @author Tony Trupp <tony@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * Base class for collection attribute and file attribute
 * @author Tony Trupp <tony@concrete5.org>
 * @package models
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

 
abstract class AttributeKey extends Object {  
  
	abstract function get($fakID);
	
	abstract function getList();
	
	function add(){
		throw new Exception( t(' Error: You must overwrite AttributeKey::add() ') );	
	}	
	
	function update(){
		throw new Exception( t(' Error: You must overwrite AttributeKey::update() ') );	
	}
	
	abstract function delete();
	
	abstract function renameValue($oldSpelling,$newSpelling);
	
	abstract function inUse($akHandle);
  
    abstract function getAttributeKeyID();	
	function getAttributeKeyHandle() {return $this->akHandle;}
	function getAttributeKeyName() {return $this->akName;}	
	function getAllowOtherValues() {return $this->akAllowOtherValues; }
	function getAttributeKeyValues() {return $this->akValues;}
	function getAttributeKeyType() {return $this->akType;}  
  
  	/* DEPRICATED */
	function AttributeKeyID(){ $this->getAttributeKeyID(); }
	function getCollectionAttributeKeyHandle() {return $this->akHandle;}
	function getCollectionAttributeKeyName() {return $this->akName;}
	function getCollectionAttributeKeyValues() {return $this->akValues;}
	function getCollectionAttributeKeyType() {return $this->akType;}
 
}
 
?>