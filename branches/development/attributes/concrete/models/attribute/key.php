<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeKey extends Object {

	/** 
	 * Returns the name for this attribute key
	 */
	public function getAttributeKeyName() { return $this->akName;}

	/** 
	 * Returns the handle for this attribute key
	 */
	public function getAttributeKeyHandle() { return $this->akHandle;}
	
	/** 
	 * Returns the ID for this attribute key
	 */
	public function getAttributeKeyID() {return $this->akID;}
	
	/** 
	 * Loads the required attribute fields for this instantiated attribute
	 */
	protected function load($akID) {
		$db = Loader::db();
		$row = $db->GetRow('select akID, akHandle, akName, akCategoryID, akIsEditable, AttributeKeys.atID, atHandle, AttributeKeys.pkgID from AttributeKeys inner join AttributeTypes on AttributeKeys.atID = AttributeTypes.atID where akID = ?', array($akID));
		$this->setPropertiesFromArray($row);
	}

	/** 
	 * Returns an attribute type object 
	 */
	public function getAttributeType() {
		return AttributeType::getByID($this->atID);
	}
	
	/** 
	 * Loads a value for a particular attribute key/valID combination
	 */
	public function getAttributeValue($avID) {
		$at = AttributeType::getByHandle($this->atHandle);
		return $at->getValue($avID);
	}

	/** 
	 * Returns a list of all attributes of this category
	 */
	protected static function getList($akCategoryHandle) {
		$db = Loader::db();
		$r = $db->Execute('select akID from AttributeKeys inner join AttributeKeyCategories on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akCategoryHandle = ?', array($akCategoryHandle));
		$list = array();
		$txt = Loader::helper('text');
		$className = $txt->camelcase($akCategoryHandle);
		while ($row = $r->FetchRow()) {
			$c1 = $className . 'AttributeKey';
			$c1a = new $c1();
			$c1a->load($row['akID']);
			$list[] = $c1a;
		}
		return $list;
	}
	
	/** 
	 * Renders a view for this attribute key. If no view is default we display it's "view"
	 * Valid views are "view", "form" or a custom view (if the attribute has one in its directory)
	 * Additionally, an attribute does not have to have its own interface. If it doesn't, then whatever
	 * is printed in the corresponding $view function in the attribute's controller is printed out.
	 */
	public function render($view = 'view', $value = false) {
		$at = AttributeType::getByHandle($this->atHandle);
		$at->render($this, $view, $value);
	}
		
}
