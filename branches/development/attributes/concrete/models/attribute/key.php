<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeKey extends Object {

	/** 
	 * Returns the handle for this attribute key
	 */
	public function getAttributeKeyHandle() { return $this->akHandle;}
	
	/** 
	 * Loads the required attribute fields for this instantiated attribute
	 */
	protected function load($akID) {
		$db = Loader::db();
		$row = $db->GetRow('select akID, akHandle, akCategoryID, akIsEditable, AttributeKeys.atID, atHandle, AttributeKeys.pkgID from AttributeKeys inner join AttributeTypes on AttributeKeys.atID = AttributeTypes.atID where akID = ?', array($akID));
		$this->setPropertiesFromArray($row);
	}

	
	/** 
	 * Loads a value for a particular attribute key/valID combination
	 */
	public function getAttributeValue($avID) {
		$at = AttributeType::getByHandle($this->atHandle);
		return $at->getValue($avID);
	}

}
