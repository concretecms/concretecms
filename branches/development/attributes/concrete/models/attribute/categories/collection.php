<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class CollectionAttributeKey extends AttributeKey {

	/** 
	 * Returns an attribute value list of attributes and values (duh) which a collection version can store 
	 * against its object.
	 * @return AttributeValueList
	 */
	public function getAttributes($cID, $cvID) {
		$db = Loader::db();
		$values = $db->GetAll("select akID, avID from CollectionAttributeValues where cID = ? and cvID = ?", array($cID, $cvID));
		$avl = new AttributeValueList();
		foreach($values as $val) {
			$ak = CollectionAttributeKey::getByID($val['akID']);
			$value = $ak->getAttributeValue($val['avID']);
			$avl->addAttributeValue($ak, $value);
		}		
		return $avl;
	}
	
	public function load($akID) {
		parent::load($akID);
		// now we load the specific fields for collection attributes
		$db = Loader::db();
		$row = $db->GetRow('select akSearchable from CollectionAttributeKeys where akID = ?', array($akID));
		$this->setPropertiesFromArray($row);
		return $ak;	
	}
		
	public static function getByID($akID) {
		$ak = new CollectionAttributeKey();
		$ak->load($akID);
		return $ak;
	}
		
	public static function getList() {
		return parent::getList('collection');	
	}
	
	/** 
	 * @access private 
	 */
	public function get($akID) {
		return CollectionAttributeKey::getByID($akID);
	}

	

}
