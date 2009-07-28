<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class DefaultAttributeType extends AttributeType  {


	public function getValue($avID) {
		$db = Loader::db();
		$value = $db->GetOne("select value from atDefault where avID = ?", array($avID));
		return $value;	
	}

	public function form($attributeKey, $attributeValue) {
		print '<textarea name="akID_' . $attributeKey->getAttributeKeyID() . '" style="width: 100%; height: 40px">' . $attributeValue . '</textarea>';
	}

	public function save() {
	
	}
}
