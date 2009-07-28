<?
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * This class holds a list of attribute values for an object. Why do we need a special class to do this? Because 
 * class can be retrieved by handle
 */
class AttributeValueList extends Object implements Iterator {
		
	private $attributes = array();
	
	public function addAttributeValue($ak, $value) {
		$this->attributes[$ak->getAttributeKeyHandle()] = $value;
	}
	
	public function __construct($array = false) {
		if (is_array($array)) {
			$this->attributes = $array;
		}
	}
	
	public function getAttribute($akHandle) {
		return $this->attributes[$akHandle];
	}
	
	public function rewind() {
		reset($this->attributes);
	}
	
	public function current() {
		return current($this->attributes);
	}
	
	public function key() {
		return key($this->attributes);
	}
	
	public function next() {
		next($this->attributes);
	}
	
	public function valid() {
		return $this->current() !== false;
	}
	
}
