<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeKeyCategory extends Object {

	public static function getByID($akCategoryID) {
		$db = Loader::db();
		$row = $db->GetRow('select akCategoryID, akCategoryHandle from AttributeKeyCategories where akCategoryID = ?', array($akCategoryID));
		$akc = new AttributeKeyCategory();
		$akc->setPropertiesFromArray($row);
		return $akc;
	}
	


}
