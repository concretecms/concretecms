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
	
	public function saveAttribute($nvc) {
		// We check a cID/cvID/akID combo, and if that particular combination has an attribute value ID that
		// is NOT in use anywhere else on the same cID, cvID, akID combo, we use it (so we reuse IDs)
		// otherwise generate new IDs
		
		$av = $nvc->getAttributeValueObject($this);
		$cnt = 0;
		
		// Is this avID in use ?
		if (is_object($av)) {
			$db = Loader::db();
			$cnt = $db->GetOne("select count(avID) from CollectionAttributeValues where avID = ?", $av->getAttributeValueID());
		}
		
		if ((!is_object($av)) || ($cnt > 1)) {
			$at = $this->getAttributeType();
			$av = $at->addAttributeValue();
		}
		
		parent::saveAttribute($av);
		$db = Loader::db();
		$v = array($nvc->getCollectionID(), $nvc->getVersionID(), $this->getAttributeKeyID(), $av->getAttributeValueID());
		$db->Replace('CollectionAttributeValues', array(
			'cID' => $nvc->getCollectionID(), 
			'cvID' => $nvc->getVersionID(), 
			'akID' => $this->getAttributeKeyID(), 
			'avID' => $av->getAttributeValueID()
		), array('cID', 'cvID', 'akID'));
	}
	
}

class CollectionAttributeValue extends AttributeValue {

	public function setCollection($cObj) {
		$this->c = $cObj;
	}
	
	public static function getByID($avID) {
		$cav = new CollectionAttributeValue();
		$cav->load($avID);
		if ($cav->getAttributeValueID() == $avID) {
			return $cav;
		}
	}

	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from CollectionAttributeValues where cID = ? and cvID = ? and akID = ? and avID = ?', array(
			$this->c->getCollectionID(), 
			$this->c->getVersionID(),
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));
	}
}