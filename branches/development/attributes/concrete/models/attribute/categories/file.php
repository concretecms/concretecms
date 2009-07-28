<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class FileAttributeKey extends AttributeKey {


	/** 
	 * Returns an attribute value list of attributes and values (duh) which a file version can store 
	 * against its object.
	 * @return AttributeValueList
	 */
	public function getAttributes($fID, $fvID) {
		$db = Loader::db();
		$values = $db->GetAll("select akID, avID from FileAttributeValues where fID = ? and fvID = ?", array($fID, $fvID));
		$avl = new AttributeValueList();
		foreach($values as $val) {
			$ak = FileAttributeKey::getByID($val['akID']);
			$value = $ak->getAttributeValue($val['avID']);
			$avl->addAttributeValue($ak, $value);
		}		
		return $avl;
	}
	
	public static function getByID($akID) {
		$ak = new FileAttributeKey();
		$ak->load($akID);
		return $ak;	
	}



}
